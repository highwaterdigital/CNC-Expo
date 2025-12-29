<?php
/**
 * Sponsorship Page Shortcode
 */
if (!defined('ABSPATH')) {
    exit;
}

ob_start();
?>
<section style="padding:120px 0 80px;">
    <div style="max-width:1200px;margin:0 auto;padding:0 24px;font-family:'Poppins',sans-serif;">
        <h1 style="text-align:center;font-size:46px;margin-bottom:12px;">Sponsorship Opportunities</h1>
        <p style="text-align:center;font-size:20px;color:#444;margin-bottom:40px;">Enhance your brand visibility at CNC Expo</p>

        <?php
        $plans = [
            [
                'title' => 'Platinum Sponsor',
                'tag' => '“Exclusive”',
                'items' => [
                    '54 sq. m Bare Space',
                    'Logo inclusion on the top row of the sponsors’ section on the website',
                    'Testimonial from a Senior Executive across communication channels',
                    'Social media content share across Instagram, Twitter, Facebook, LinkedIn',
                    'Logo featured as Platinum Sponsor on sponsor panel',
                    'Logo on hall entrance gate banners',
                    'Logo on onsite digital screens',
                    'Logo on conference backdrops',
                    'Full page color advertisement in the brochure',
                    'Company logo on registration bags (both sides)',
                    'Logo on promotional materials (Banners, Posters, Hoardings, Magazines, Passes, Tags)',
                    'Space for 10 standees',
                    'Organizer email to industry database',
                    'One 60-minute speaking slot in the conference',
                    'Post-event social media “Thank You Sponsor” posts',
                    'Logo & sponsorship title in the Post-Show Report',
                ],
                'price' => 'INR 18,00,000 + GST @ 18%',
            ],
            [
                'title' => 'Diamond Sponsor',
                'tag' => '“Exclusive”',
                'items' => [
                    '48 sq. m Bare Space',
                    'Top row logo on sponsor section of the website',
                    'Senior executive testimonials across channels',
                    'Opportunity to share content on social media',
                    'Logo as Diamond Sponsor on sponsor panel',
                    'Logo on entrance gate banners',
                    'Logo on digital screens & conference backdrops',
                    'Full page color advertisement in brochure',
                    'Logo on registration bags (two sides)',
                    'Logo on promotional materials',
                    'Space for 6 standees',
                    'Email blast to industry database',
                    'One 45-minute speaking slot',
                    'Social media “Thank You Sponsor” posts',
                    'Logo mention in Post-Show Report',
                ],
                'price' => 'INR 15,00,000 + GST @ 18%',
            ],
            [
                'title' => 'Gold Sponsor',
                'tag' => '“Non-Exclusive – 2 Only”',
                'items' => [
                    '36 sq. m Bare Space',
                    'Logo top row on sponsor section',
                    'Executive testimonials',
                    'Social media content sharing',
                    'Logo as Gold Sponsor on panel',
                    'Logo on entrance gate banners',
                    'Logo on digital screens & conference backdrops',
                    'Full page ad in brochure',
                    'Logo on bags (two sides)',
                    'Logo on promotional collateral',
                    'One 30-minute speaking slot',
                    'Social media Thank You posts',
                    'Logo in Post-Show Report',
                ],
                'price' => 'INR 12,00,000 + GST @ 18%',
            ],
            [
                'title' => 'Silver Sponsor',
                'tag' => '“Non-Exclusive – 3 Only”',
                'items' => [
                    '27 sq. m Built-up stall',
                    'Half page ad in brochure',
                    'Logo in website sponsor section',
                    'Social media content share',
                    'Logo as Silver Sponsor on panel',
                    'Logo on entrance gate banners & materials',
                    'One 30-minute speaking slot',
                    'Social media Thank You posts',
                    'Logo in Post-Show Report',
                ],
                'price' => 'INR 9,00,000 + GST @ 18%',
            ],
            [
                'title' => 'Associate Sponsors',
                'tag' => '“Non-Exclusive – 4 Only”',
                'items' => [
                    '18 sq. m Built-up stall',
                    'Half page ad in brochure',
                    'Logo on entrance & banner signage',
                    'Logo on promotional materials',
                    'Social media Thank You posts',
                    'Logo in Post-Show Report',
                ],
                'price' => 'INR 7,00,000 + GST @ 18%',
            ],
        ];
        foreach ($plans as $plan):
            ?>
            <div style="background:#fafafa;padding:30px;border-radius:12px;margin-bottom:40px;border:1px solid #e5e5e5;box-shadow:0 4px 14px rgba(0,0,0,0.08);">
            <div style="font-size:32px;font-weight:700;margin-bottom:10px;color:#d19c00;">
                    <?php echo esc_html($plan['title']); ?> <span style="color:#000;"><?php echo $plan['tag']; ?></span>
                </div>
                <ul style="margin:0;padding-left:20px;line-height:1.7;font-size:17px;color:#666;">
                    <?php foreach ($plan['items'] as $item): ?>
                        <li><?php echo esc_html($item); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div style="font-size:20px;font-weight:600;margin-top:25px;color:#aa0000;"><?php echo esc_html($plan['price']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
return ob_get_clean();
