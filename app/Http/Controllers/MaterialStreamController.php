<?php

namespace App\Http\Controllers;

use App\Models\StudyMaterial;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialStreamController extends Controller
{
    public function show(StudyMaterial $studyMaterial): StreamedResponse
    {
        abort_unless($studyMaterial->isVideo() && $studyMaterial->file_path, 404);
        abort_unless($this->canView($studyMaterial), 403);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($studyMaterial->file_path), 404);

        $path = $disk->path($studyMaterial->file_path);
        $size = filesize($path);
        $start = 0;
        $end = $size - 1;
        $status = 200;

        if (request()->headers->has('Range')) {
            $range = request()->header('Range');

            if (preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
                $status = 206;

                if ($matches[1] !== '') {
                    $start = (int) $matches[1];
                }

                if ($matches[2] !== '') {
                    $end = min((int) $matches[2], $end);
                }
            }
        }

        $start = max(0, min($start, $size - 1));
        $end = max($start, min($end, $size - 1));
        $length = $end - $start + 1;
        $mime = strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'webm' ? 'video/webm' : 'video/mp4';

        $headers = [
            'Content-Type' => $mime,
            'Content-Length' => (string) $length,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=0, no-cache',
        ];

        if ($status === 206) {
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
        }

        return response()->stream(function () use ($path, $start, $length) {
            $stream = fopen($path, 'rb');
            fseek($stream, $start);

            $remaining = $length;
            while ($remaining > 0 && !feof($stream)) {
                $chunkSize = min(8192, $remaining);
                echo fread($stream, $chunkSize);
                flush();
                $remaining -= $chunkSize;
            }

            fclose($stream);
        }, $status, $headers);
    }

    private function canView(StudyMaterial $material): bool
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return optional($user->channel)->id === $material->channel_id;
        }

        if ($material->is_free || optional($material->course)->is_free) {
            return true;
        }

        if (!$material->course_id) {
            return false;
        }

        return Subscription::where('student_id', $user->id)
            ->where('course_id', $material->course_id)
            ->where('status', 'active')
            ->exists();
    }
}
