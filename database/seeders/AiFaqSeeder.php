<?php

namespace Database\Seeders;

use App\Models\AiFaq;
use Illuminate\Database\Seeder;

class AiFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // ── STUDENT ──────────────────────────────────────────────────────
            ['role' => 'student', 'category' => 'Account', 'question' => 'How do I create an account?',
             'answer' => "Follow these steps to create your EduVerse account:\n\n1. Visit the homepage at the LMS URL.\n2. Click the **Get Started** button in the top-right corner.\n3. Fill in your **Name**, **Email**, and **Password**.\n4. Click **Register**.\n5. Check your email inbox for a verification link.\n6. Click the verification link to activate your account.\n7. You will be automatically logged in and redirected to your dashboard.\n\n> Tip: Use a valid email address — you'll need it to reset your password and receive course notifications.",
             'keywords' => ['create', 'account', 'register', 'sign up', 'join']],

            ['role' => 'student', 'category' => 'Account', 'question' => 'How do I log in?',
             'answer' => "To log in to EduVerse:\n\n1. Click **Login** in the top navigation bar.\n2. Enter your registered **Email** and **Password**.\n3. Click **Sign In**.\n4. You will be redirected to your **Student Dashboard**.\n\nIf you forgot your password, click the **Forgot Password?** link on the login page.",
             'keywords' => ['login', 'sign in', 'log in', 'access account']],

            ['role' => 'student', 'category' => 'Account', 'question' => 'How do I verify my email?',
             'answer' => "Email verification is required to access all features:\n\n1. After registering, check your **inbox** for an email from EduVerse.\n2. Click the **Verify Email Address** button in the email.\n3. If you didn't receive the email, log in and click **Resend Verification Email**.\n4. Check your **Spam/Junk** folder if you don't see it.\n\nYour account will be fully activated once verified.",
             'keywords' => ['verify', 'email', 'verification', 'confirm email']],

            ['role' => 'student', 'category' => 'Account', 'question' => 'How do I reset my password?',
             'answer' => "To reset your password:\n\n1. Go to the **Login** page.\n2. Click **Forgot Password?**\n3. Enter your registered email address.\n4. Click **Send Reset Link**.\n5. Check your email for the password reset link.\n6. Click the link and enter your **new password**.\n7. Confirm the new password and click **Reset Password**.\n\nThe reset link expires in 60 minutes. If expired, request a new one.",
             'keywords' => ['reset', 'password', 'forgot password', 'change password']],

            ['role' => 'student', 'category' => 'Courses', 'question' => 'How do I purchase a course?',
             'answer' => "To purchase a course on EduVerse:\n\n1. Go to **Browse** from the navigation menu.\n2. Find the course you want and click on it.\n3. Review the course details (lessons, duration, price).\n4. Click **Subscribe to Course** or **Enroll Now**.\n5. If the course is **free**, you'll be enrolled instantly.\n6. If the course is **paid**, you'll proceed to the payment page.\n7. Select your payment method (Razorpay / Stripe / PayPal).\n8. Complete the payment.\n9. You'll be redirected to your **My Courses** page.\n\nYou can start learning immediately after enrollment!",
             'keywords' => ['purchase', 'buy', 'course', 'enroll', 'subscribe', 'payment']],

            ['role' => 'student', 'category' => 'Courses', 'question' => 'How do I apply a coupon code?',
             'answer' => "To apply a coupon code during checkout:\n\n1. Go to the course detail page.\n2. Click **Subscribe / Enroll**.\n3. On the checkout page, look for the **Coupon Code** field.\n4. Enter your coupon code and click **Apply**.\n5. The discount will be applied automatically to your total.\n6. Complete the payment with the discounted price.\n\nCoupon codes are case-sensitive. Contact support if your coupon is not working.",
             'keywords' => ['coupon', 'discount code', 'promo code', 'voucher', 'apply coupon']],

            ['role' => 'student', 'category' => 'Courses', 'question' => 'How do I view my purchased courses?',
             'answer' => "To view your enrolled courses:\n\n1. Log in to your account.\n2. Click **My Courses** in the navigation bar.\n3. You'll see all your active subscriptions with expiry dates.\n4. Click **Continue Learning** on any course to resume.\n\nYou can also access your courses directly from your **Student Dashboard**.",
             'keywords' => ['my courses', 'purchased', 'enrolled', 'view courses', 'subscriptions']],

            ['role' => 'student', 'category' => 'Learning', 'question' => 'How do I watch course videos?',
             'answer' => "To watch course videos:\n\n1. Go to **My Courses** and select a course.\n2. Click **Continue Learning** or **Start Learning**.\n3. You'll enter the **Learning Room**.\n4. The video player will load automatically.\n5. Use the left sidebar to navigate between lessons.\n6. Your progress is saved automatically as you watch.\n\nTip: You can also read PDF notes alongside each lesson in the Learning Room.",
             'keywords' => ['watch', 'video', 'play', 'lesson', 'learning room', 'video player']],

            ['role' => 'student', 'category' => 'Account', 'question' => 'How do I update my profile?',
             'answer' => "To update your profile:\n\n1. Log in to your account.\n2. Click the **Profile** link in the navigation (or your avatar).\n3. You can update:\n   - Your **Name**\n   - **Bio**\n   - **Phone number**\n   - **Profile photo**\n4. Click **Save Changes**.\n\nYour email address cannot be changed after registration.",
             'keywords' => ['profile', 'update', 'edit profile', 'change name', 'avatar', 'bio']],

            ['role' => 'student', 'category' => 'Support', 'question' => 'How do I contact support?',
             'answer' => "To contact EduVerse support:\n\n1. Use the **Community** forum to post your question — teachers and moderators respond quickly.\n2. Email us at the contact address shown on the homepage.\n3. Use this AI Assistant to get instant help for common questions.\n\nFor urgent payment issues, include your **payment ID** or **order ID** in your message.",
             'keywords' => ['contact', 'support', 'help', 'customer service', 'issue', 'problem']],

            ['role' => 'student', 'category' => 'Courses', 'question' => 'How do I download a certificate?',
             'answer' => "Certificates are awarded after completing a course or passing a test series:\n\n1. Complete all lessons **or** pass the required test series.\n2. Go to **My Courses**.\n3. Click on the completed course.\n4. Click **Download Certificate**.\n5. Your certificate will download as a PDF.\n\nCertificates include your name, course title, and completion date.",
             'keywords' => ['certificate', 'download', 'completion', 'achievement']],

            // ── TEACHER ──────────────────────────────────────────────────────
            ['role' => 'teacher', 'category' => 'Channel', 'question' => 'How do I create my teacher channel?',
             'answer' => "To set up your teacher channel:\n\n1. Log in as a **Teacher**.\n2. You'll be prompted to create a channel on your first login.\n3. Fill in your **Channel Name** and **Description**.\n4. Upload a **Logo** (optional).\n5. Select a **Subscription Plan** (free or paid).\n6. Click **Create Channel**.\n7. If on a paid plan, complete payment to activate your channel.\n\nOnce active, you can start creating courses and uploading content.",
             'keywords' => ['channel', 'create channel', 'teacher channel', 'setup']],

            ['role' => 'teacher', 'category' => 'Courses', 'question' => 'How do I create a course?',
             'answer' => "To create a new course:\n\n1. Go to your **Teacher Dashboard**.\n2. Click **Courses** in the sidebar.\n3. Click **+ Create Course**.\n4. Fill in:\n   - **Course Title**\n   - **Subject** (category)\n   - **Description**\n   - **Price** (enter 0 for free)\n   - **Duration** (days students get access)\n   - **Level** (High School / Graduate / Master)\n   - **Thumbnail** image\n5. Check **Publish** if ready to go live.\n6. Click **Create Course**.\n\nAfter creating the course, add lessons from the Lessons section.",
             'keywords' => ['create course', 'new course', 'add course', 'publish course']],

            ['role' => 'teacher', 'category' => 'Lessons', 'question' => 'How do I upload videos and add lessons?',
             'answer' => "To add lessons to a course:\n\n1. Go to **Courses** in your dashboard.\n2. Click on the course you want to add lessons to.\n3. Click **Manage Lessons**.\n4. Click **+ Add Lesson**.\n5. Fill in:\n   - **Lesson Title**\n   - **Video URL** (YouTube/Vimeo embed) OR upload a **Video File**\n   - **PDF Notes** (optional)\n   - **Description**\n   - Check **Free Preview** if you want this lesson visible to non-subscribers\n6. Click **Save Lesson**.\n\nLessons are ordered by the sequence you add them.",
             'keywords' => ['upload', 'video', 'lesson', 'add lesson', 'content']],

            ['role' => 'teacher', 'category' => 'Tests', 'question' => 'How do I create a quiz or test series?',
             'answer' => "To create a test series:\n\n1. Go to **Test Series** in your dashboard.\n2. Click **+ Create Test Series**.\n3. Fill in the title, select the course, and set total/passing marks.\n4. Click **Create**.\n5. Now add **Sections** to the test series.\n6. For each section, add **Questions** with options:\n   - MCQ (single correct)\n   - MSQ (multiple correct)\n   - NAT (numerical answer)\n7. Set marks and negative marks per question.\n8. Publish when ready.\n\nStudents must complete sections in order (progressive unlocking).",
             'keywords' => ['quiz', 'test', 'test series', 'exam', 'questions', 'create test']],

            ['role' => 'teacher', 'category' => 'Earnings', 'question' => 'How do I view my earnings and revenue?',
             'answer' => "To view your earnings:\n\n1. Go to your **Teacher Dashboard**.\n2. Look at the **Stats** section — it shows:\n   - Total revenue earned\n   - Number of enrolled students\n   - Total courses and materials\n3. Scroll down to see **Recent Subscriptions** — these show individual student enrollments and amounts paid.\n\nFor detailed reports, check each course's enrollment list.",
             'keywords' => ['earnings', 'revenue', 'income', 'money', 'sales', 'withdraw']],

            ['role' => 'teacher', 'category' => 'Students', 'question' => 'How do I manage my students?',
             'answer' => "To view and manage students enrolled in your courses:\n\n1. Go to your **Teacher Dashboard**.\n2. Check the **Recent Subscriptions** section for student activity.\n3. You can see which students enrolled in which courses.\n4. Use the **Community** feature to interact with students, answer questions, and post updates.\n\nFor course-specific student management, click on a course and view its subscriber list.",
             'keywords' => ['students', 'manage students', 'enrolled students', 'subscribers']],

            ['role' => 'teacher', 'category' => 'Bundles', 'question' => 'How do I create a course bundle?',
             'answer' => "To create a course bundle:\n\n1. Go to **Bundles** in your dashboard.\n2. Click **+ Create Bundle**.\n3. Fill in:\n   - **Bundle Title** and **Description**\n   - Select **courses** to include\n   - Set the **Bundle Price** (usually discounted)\n   - Add **collaborating teachers** if bundling with others\n4. Click **Create Bundle**.\n\nCollaborating teachers will receive an invite to accept the bundle collaboration.",
             'keywords' => ['bundle', 'course bundle', 'package', 'collaboration']],

            // ── ADMIN ─────────────────────────────────────────────────────────
            ['role' => 'admin', 'category' => 'Dashboard', 'question' => 'How do I view platform statistics?',
             'answer' => "As an admin, your dashboard shows real-time platform stats:\n\n- **Total Students** — all registered students\n- **Total Teachers** — all registered teachers\n- **Total Courses** — published and draft courses\n- **Total Channels** — teacher channels\n- **Active Subscriptions** — current paid enrollments\n- **Total Revenue** — sum of all payments\n\nScroll down to see **Recent Users** and **Recent Subscriptions**.\n\nYou can also ask me: 'How many students do we have?' and I'll query the live database for you!",
             'keywords' => ['statistics', 'stats', 'dashboard', 'overview', 'analytics', 'reports']],

            ['role' => 'admin', 'category' => 'Users', 'question' => 'How do I manage users?',
             'answer' => "To manage users:\n\n1. Go to **Admin Panel** → **Users**.\n2. Filter by role: Student / Teacher.\n3. You can:\n   - **Block** a user from community features.\n   - **View** user details.\n4. Use the search/filter to find specific users.\n\nTo change a user's role, use Laravel Tinker or the Spatie Permission dashboard.",
             'keywords' => ['users', 'manage users', 'block user', 'roles', 'students list', 'teachers list']],

            ['role' => 'admin', 'category' => 'Channels', 'question' => 'How do I verify or block a teacher channel?',
             'answer' => "To manage teacher channels:\n\n1. Go to **Admin Panel** → **Channels**.\n2. You'll see all teacher channels with their status.\n3. Click **Verify** to give a channel the verified badge.\n4. Click **Toggle Active** to enable or disable a channel.\n\nVerified channels' courses appear in featured listings and are trusted by students.",
             'keywords' => ['verify', 'channel', 'approve', 'block channel', 'teacher channel']],

            // ── ALL ROLES ─────────────────────────────────────────────────────
            ['role' => 'all', 'category' => 'Community', 'question' => 'How do I use the community forum?',
             'answer' => "The EduVerse Community is where students, teachers, and admins interact:\n\n1. Click **Community** in the navigation.\n2. Browse **Forum Groups** by subject.\n3. Click a group to view posts.\n4. Click **+ New Post** to ask a question or share an update.\n5. Comment on posts and **mark answers** as helpful.\n6. Use **Likes** to appreciate good content.\n\nCommunity is a great place to get help from peers and teachers!",
             'keywords' => ['community', 'forum', 'discussion', 'post', 'question', 'interact']],
        ];

        foreach ($faqs as $faq) {
            AiFaq::updateOrCreate(
                ['question' => $faq['question'], 'role' => $faq['role']],
                array_merge($faq, ['is_active' => true])
            );
        }

        $this->command->info('✅ Seeded ' . count($faqs) . ' AI FAQs successfully!');
    }
}
