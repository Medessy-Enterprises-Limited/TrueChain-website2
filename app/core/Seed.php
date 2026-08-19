<?php
/**
 * First-install content seeding: settings, pages, blocks, sliders,
 * companies, leadership. Everything seeded here is editable in the
 * admin panel afterwards.
 */
class Seed
{
    /**
     * Bumped whenever content shipped with the code changes. Sites installed
     * before the bump run Seed::topUp() once to catch up.
     */
    public const CONTENT_VERSION = 3;

    public static function run(string $adminName, string $adminEmail, string $adminPassword): void
    {
        $now = date('Y-m-d H:i:s');

        /* ------------------------------------------------ users */
        DB::insert('users', [
            'name'          => $adminName,
            'email'         => strtolower($adminEmail),
            'password_hash' => password_hash($adminPassword, PASSWORD_DEFAULT),
            'role'          => 'admin',
            'active'        => 1,
            'created_at'    => $now,
        ]);

        /* ------------------------------------------------ settings */
        $settings = [
            'site_name'         => 'True Chain Infrastructure Company',
            'site_short'        => 'True Chain',
            'tagline'           => 'The infrastructure backbone of African road freight',
            'meta_title'        => 'True Chain Infrastructure Company | Logistics Technology, Training and Corridor Infrastructure',
            'meta_description'  => 'True Chain Infrastructure Company (TCIC) is the Nigerian holding group behind True Chain Technologies, True Chain Institute and Truck Transit Park: an integrated ecosystem making road freight safe, efficient and globally competitive.',
            'logo'              => 'assets/img/logo.png',
            'logo_white'        => 'assets/img/logo-white.png',
            'favicon'           => 'assets/img/favicon.png',
            'contact_email'     => $adminEmail,
            'contact_phone'     => '+234 800 000 0000',
            'contact_address'   => '10A Poroku Layout (Alternative Route), Off Chevron Drive, Lekki, Lagos, Nigeria',
            'office_hours'      => 'Monday to Friday, 8:00 to 17:00 WAT',
            'social_linkedin'   => '',
            'social_x'          => '',
            'social_facebook'   => '',
            'social_instagram'  => '',
            'social_youtube'    => '',
            'notify_on_contact' => '0',
            'notify_email'      => $adminEmail,
            'maintenance_mode'  => '0',
            'timezone'          => 'Africa/Lagos',
            'analytics_code'    => '',
            'copyright'         => 'True Chain Infrastructure Company. All rights reserved.',
            'content_version'   => (string)self::CONTENT_VERSION,
        ];
        foreach ($settings as $k => $v) {
            DB::insert('settings', ['skey' => $k, 'svalue' => $v]);
        }

        /* ------------------------------------------------ sliders */
        $sliders = [
            [
                'title'    => 'The backbone of trusted African logistics',
                'subtitle' => 'One group building the technology, talent and corridor infrastructure that make road freight safe, efficient and globally competitive.',
                'image'    => 'assets/img/hero-1.svg',
                'cta_text' => 'Explore the group', 'cta_url' => 'companies',
                'cta2_text' => 'Partner with us', 'cta2_url' => 'contact',
            ],
            [
                'title'    => 'Three companies. One integrated chain.',
                'subtitle' => 'True Chain Technologies, True Chain Institute and Truck Transit Park operate as one ecosystem, connected by a single driver record: the True Chain ID.',
                'image'    => 'assets/img/hero-2.svg',
                'cta_text' => 'How the chain works', 'cta_url' => 'about',
                'cta2_text' => 'Our companies', 'cta2_url' => 'companies',
            ],
            [
                'title'    => 'Security you can measure. Impact you can audit.',
                'subtitle' => 'From 24/7 monitored corridors to verified driver credentials, every service is engineered to international governance, ESG and data protection standards.',
                'image'    => 'assets/img/hero-3.svg',
                'cta_text' => 'About the group', 'cta_url' => 'about',
                'cta2_text' => 'Contact us', 'cta2_url' => 'contact',
            ],
        ];
        foreach ($sliders as $i => $s) {
            DB::insert('sliders', $s + ['sort_order' => ($i + 1) * 10, 'active' => 1]);
        }

        /* ------------------------------------------------ blocks */
        $blocks = [
            [
                'identifier' => 'home-intro',
                'title'      => 'One group. One platform. One chain of trust.',
                'note'       => 'Heading and paragraph of the “About the group” section on the home page.',
                'content'    => '<p>True Chain Infrastructure Company (TCIC) is the Nigerian holding company behind a portfolio of operationally interconnected, legally separable companies built to remove the binding constraints of African road freight: cargo insecurity, market fragmentation, the rogue-driver recycle problem, the skills deficit, missing corridor infrastructure, and the energy transition.</p><p>Each company stands on its own. Together, they form one integrated chain in which every driver is trained, verified, monitored and supported from classroom to corridor.</p>',
            ],
            [
                'identifier' => 'home-stats',
                'title'      => 'Group at a glance',
                'note'       => 'The statistics band on the home page. Keep the same structure when editing.',
                'content'    => '<div class="stat-item"><span class="stat-value">220+</span><span class="stat-label">Vehicles in the anchor operating fleet</span></div>'
                    . '<div class="stat-item"><span class="stat-value">25+</span><span class="stat-label">Operational locations nationwide</span></div>'
                    . '<div class="stat-item"><span class="stat-value">11+</span><span class="stat-label">Years serving FMCG multinationals</span></div>'
                    . '<div class="stat-item"><span class="stat-value">6</span><span class="stat-label">Geopolitical zones in program scope</span></div>',
            ],
            [
                'identifier' => 'home-ecosystem-intro',
                'title'      => 'One driver record across the entire chain',
                'note'       => 'Intro text above the ecosystem flow diagram on the home page.',
                'content'    => '<p>The True Chain ID (TCID) is the permanent professional credential that follows every commercial driver across the group: training records from the Institute, verification and integrity scoring on the Registry, live telematics from the Security Operations Centre, coordinated trips on the Collaborative Logistics Network, and verified rest stops at the Truck Transit Parks.</p>',
            ],
            [
                'identifier' => 'home-cta',
                'title'      => 'Build the future of African logistics with us',
                'note'       => 'Closing call-to-action band on the home page.',
                'content'    => '<p>We work with FMCG principals, carriers, insurers, development finance institutions and public agencies. If trusted, efficient road freight matters to your organisation, we should talk.</p>',
            ],
            [
                'identifier' => 'footer-about',
                'title'      => '',
                'note'       => 'Short paragraph under the logo in the footer.',
                'content'    => '<p>The holding group behind True Chain Technologies, True Chain Institute and Truck Transit Park: one integrated ecosystem for safe, efficient and globally competitive road freight.</p>',
            ],
            [
                'identifier' => 'contact-intro',
                'title'      => 'Let’s talk',
                'note'       => 'Intro text on the contact page.',
                'content'    => '<p>Whether you are an FMCG principal, a carrier, an insurer, an investor or a public agency, our team will route your enquiry to the right company in the group and respond promptly.</p>',
            ],
        ];
        foreach ($blocks as $b) {
            DB::insert('blocks', $b + ['active' => 1]);
        }

        /* ------------------------------------------------ companies */
        foreach (self::companies() as $i => $c) {
            DB::insert('companies', $c + ['sort_order' => ($i + 1) * 10, 'active' => 1]);
        }

        /* ------------------------------------------------ leadership */
        foreach (self::leaders() as $l) {
            DB::insert('leaders', $l);
        }

        /* ------------------------------------------------ pages */
        foreach (self::pages() as $i => $p) {
            DB::insert('pages', $p + ['created_at' => $now, 'updated_at' => $now]);
        }
    }

    /**
     * Bring an existing installation in line with the seeded leadership and
     * head office address. Only fills gaps: leaders already present by name are
     * left alone, and the address is refreshed only while it still holds the old
     * placeholder, so anything edited in the admin panel is never overwritten.
     */
    public static function topUp(): void
    {
        foreach (self::leaders() as $l) {
            $existing = DB::get(
                'SELECT id, photo FROM ' . DB::table('leaders') . ' WHERE name = ?',
                [$l['name']]
            );
            if ($existing === null) {
                DB::insert('leaders', $l);
                continue;
            }
            // Fill in a portrait that shipped later, but never replace one
            // chosen in the admin panel.
            if ($l['photo'] !== '' && ($existing['photo'] ?? '') === '') {
                DB::update('leaders', ['photo' => $l['photo']], 'id = ?', [$existing['id']]);
            }
        }

        foreach (self::companyWebsites() as $slug => $url) {
            $current = DB::get(
                'SELECT website_url FROM ' . DB::table('companies') . ' WHERE slug = ?',
                [$slug]
            );
            if ($current !== null && (($current['website_url'] ?? '') === '' || $current['website_url'] === '#')) {
                DB::update(
                    'companies',
                    ['website_url' => $url, 'site_status' => 'live'],
                    'slug = ?',
                    [$slug]
                );
            }
        }

        $address = DB::val(
            'SELECT svalue FROM ' . DB::table('settings') . " WHERE skey = 'contact_address'"
        );
        if ($address === 'Lagos, Nigeria') {
            DB::update(
                'settings',
                ['svalue' => '10A Poroku Layout (Alternative Route), Off Chevron Drive, Lekki, Lagos, Nigeria'],
                "skey = 'contact_address'"
            );
        }

        Settings::set('content_version', (string)self::CONTENT_VERSION);
    }

    /**
     * Live public websites for the operating companies, keyed by slug.
     *
     * @return array<string, string>
     */
    public static function companyWebsites(): array
    {
        return [
            'true-chain-registry'  => 'https://truechainregistry.com',
            'true-chain-soc'       => 'https://truechainsoc.com',
            'true-chain-institute' => 'https://truechaininstitute.com',
            'medessy-enterprises'  => 'https://medessy.com',
        ];
    }

    /* ==================================================== leadership */
    /**
     * The group leadership, mirroring the team published on medessy.com.
     * Also used by Env::provision() to top up an existing installation.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function leaders(): array
    {
        return [
            [
                'name'       => 'Dr. Osamede Evbakhavbokun',
                'title'      => 'Founder and Group Chief Executive Officer',
                'photo'      => 'https://www.medessy.com/web/image/8148-197bab08/osamede.webp',
                'linkedin'   => '',
                'email'      => '',
                'sort_order' => 10,
                'active'     => 1,
                'bio'        => '<p>Dr. Osamede Evbakhavbokun is a results-driven supply chain strategist, logistics entrepreneur and institutional innovator with over 20 years of experience spanning finance, payments and third-party logistics across Sub-Saharan Africa.</p>'
                    . '<p>As Founder and Chief Executive Officer of Medessy Enterprises Limited, he has built one of Nigeria’s foremost third-party logistics companies, managing a fleet of over 220 vehicles across more than 25 operational locations and serving the primary distribution networks of some of the world’s most recognised FMCG multinationals, including Nigerian Bottling Company (Coca-Cola), International Breweries (AB InBev) and Chi Limited.</p>'
                    . '<p>He holds a Doctor of Business Administration in Operations and Supply Chain Management from Pan-Atlantic University and Lagos Business School, where his doctoral research examined the critical success factors of third-party logistics services in Nigeria’s FMCG sector. His education further includes an MBA from Lagos Business School, a Global Leadership Program certificate from the Smith School of Business at Queen’s University, Canada, and a B.Sc. from the University of Benin.</p>'
                    . '<p>Before founding Medessy, Dr. Osamede held senior roles in Nigeria’s financial services sector, including In-bound Payments Manager at United Bank for Africa, where he oversaw monthly collections exceeding USD 1.375 billion across the group, and Head of Prepaid Cards covering 19 UBA affiliate countries. He is a recipient of the BusinessDay Top 100 Fastest Growing SMEs in Nigeria Award.</p>'
                    . '<p>Under his leadership, the group has structured institutional credit facilities exceeding NGN 10 billion for fleet expansion, pioneered a CNG dual-fuel fleet conversion programme, and designed the proprietary platforms that now anchor True Chain Technologies.</p>',
            ],
            [
                'name'       => 'Esi Evbakhavbokun',
                'title'      => 'Deputy Managing Director',
                'photo'      => 'https://www.medessy.com/web/image/8146-dfce12a9/esi.webp',
                'linkedin'   => '',
                'email'      => '',
                'sort_order' => 20,
                'active'     => 1,
                'bio'        => '<p>Esi Evbakhavbokun is Deputy Managing Director of the group, supporting its strategic direction and overseeing day-to-day operations across the operating companies.</p>'
                    . '<p>She brings almost two decades of management consulting experience across transportation, oil and gas, manufacturing and the telecommunications service industry, with a track record in project management and business process management.</p>'
                    . '<p>She holds a B.Eng. from the University of Benin, Benin City, and is a Certified SAP Finance Consultant.</p>',
            ],
            [
                'name'       => 'Oghie Ojior',
                'title'      => 'Senior Advisor',
                'photo'      => 'https://www.medessy.com/web/image/8147-a33f075d/oghior.webp',
                'linkedin'   => '',
                'email'      => '',
                'sort_order' => 30,
                'active'     => 1,
                'bio'        => '<p>Oghie Ojior is a seasoned business leader and supply chain specialist with more than 12 years of experience across industrial manufacturing, chemicals, automotive and mobility in multinational environments.</p>'
                    . '<p>He built and led the group’s chemicals import and procurement division, developing it into a core capability serving tier-one FMCG clients in Nigeria, including Nigerian Bottling Company and International Breweries (AB InBev).</p>'
                    . '<p>He helped lay the operational and strategic foundation for the group’s Trade-as-a-Service platform, building end-to-end procurement frameworks that cover demand aggregation, international sourcing, trade finance structuring, regulatory compliance with NAFDAC, SON and Customs, and last-mile logistics.</p>',
            ],
        ];
    }

    /* ==================================================== companies */
    private static function companies(): array
    {
        return [
            [
                'slug' => 'true-chain-registry',
                'name' => 'True Chain Registry',
                'short_name' => 'Registry',
                'category' => 'True Chain Technologies',
                'icon' => 'registry',
                'tagline' => 'The trusted identity and integrity record for every commercial driver.',
                'summary' => 'An industry-wide driver vetting and integrity platform that issues the True Chain ID (TCID), operates an evidence-scored Integrity Registry, and gives carriers, principals and insurers a defensible picture of every driver they engage.',
                'website_url' => 'https://truechainregistry.com',
                'site_status' => 'live',
                'content' => '<h2>What the Registry does</h2><p>The True Chain Registry is the integrity backbone of the group. Every commercial driver who completes verification receives a True Chain ID (TCID), a permanent, tamper-evident professional credential in the format TC-YEAR-STATE-SEQUENCE. The TCID is the primary key that follows the driver across every platform in the ecosystem.</p><h3>Key capabilities</h3><ul><li>Phased driver verification, including national identity (NIN), licence verification with FRSC, biometric facial indexing and liveness detection.</li><li>A three-tier, evidence-scored Integrity Registry with structured publication standards and notification of every affected driver within 24 hours.</li><li>Driver dispute resolution through an Independent Dispute Panel, with a formal filing process and published outcomes.</li><li>Company portal with six driver search methods, including photo match, case submission, billing and a full audit log.</li><li>Training records written directly from the True Chain Institute to each driver’s TCID.</li><li>Insurer data licensing and structured underwriting queries.</li></ul><h3>Who it serves</h3><p>FMCG principals verifying drivers across their haulier base, 3PLs and carriers hiring at scale, insurers pricing risk on real evidence, and professional drivers who finally own a portable, verifiable career record.</p><h3>Built for data protection</h3><p>The platform is engineered for the Nigeria Data Protection Act 2023: explicit consent capture at registration, purpose-limited role-based access, query audit trails, and driver rights of access and correction.</p>',
            ],
            [
                'slug' => 'true-chain-soc',
                'name' => 'True Chain Security Operations Centre',
                'short_name' => 'SOC',
                'category' => 'True Chain Technologies',
                'icon' => 'soc',
                'tagline' => 'Real-time security, telematics and rescue for every monitored kilometre.',
                'summary' => 'A 24/7 security operations centre that bundles nine integrated services, from cargo cameras and ePadlocks to AI driver monitoring, panic SOS and corridor rescue, into one per-vehicle subscription.',
                'website_url' => 'https://truechainsoc.com',
                'site_status' => 'live',
                'content' => '<h2>Nine services. One subscription.</h2><p>The SOC turns every monitored truck into a connected, protected asset. A single per-vehicle subscription bundles the full security and telematics stack:</p><ul><li><strong>Cargo Camera</strong> monitoring of the load compartment.</li><li><strong>Tracking and telematics</strong> with live corridor visibility.</li><li><strong>Cargo ePadlock</strong> tamper-seal monitoring.</li><li><strong>AI cameras</strong> detecting fatigue, distraction and seat-belt compliance.</li><li><strong>Police collaboration</strong> through structured response protocols.</li><li><strong>In-cabin communications</strong> with the control room.</li><li><strong>Fuel monitoring</strong> against theft and diversion.</li><li><strong>Panic SOS</strong> for drivers in distress.</li><li><strong>Rescue services</strong>: tow capability, medical response framework agreements and fixed-point breakdown support anchored at the Truck Transit Parks.</li></ul><h3>The Pre-Trip Inspection App</h3><p>No monitored trip starts without it. The dispatcher scans the driver’s TCID, captures a fresh facial match against the Registry biometric record, verifies vehicle condition, and the SOC operator approves departure. Every approved trip writes to the driver’s permanent operating history.</p><h3>From telemetry to training</h3><p>The SOC’s AI engine recognises behavioural patterns, recurring harsh braking, distraction flags, fatigue cues, and recommends targeted retraining at the True Chain Institute, closing the loop between monitoring and professional development.</p>',
            ],
            [
                'slug' => 'true-chain-cln',
                'name' => 'True Chain Collaborative Logistics Network',
                'short_name' => 'CLN',
                'category' => 'True Chain Technologies',
                'icon' => 'cln',
                'tagline' => 'Shared capacity. Fewer empty kilometres. Lower cost per tonne.',
                'summary' => 'A collaborative freight coordination platform that matches loads to available capacity across fleets, cutting empty running, lowering cost per tonne-kilometre and reducing emissions.',
                'website_url' => '#',
                'site_status' => 'coming-soon',
                'content' => '<h2>Why collaboration wins</h2><p>Nigerian road freight is deeply fragmented: trucks run empty on return legs while loads wait elsewhere for capacity. The CLN coordinates fleets, within corporate groups and across an open network, so that capacity and cargo find each other.</p><h3>How it works</h3><ul><li><strong>Trip Work Orders</strong> define every coordinated movement with transparent costing.</li><li><strong>Intra-group fleet sharing</strong> runs on a transparent coordination fee, typically 6 percent of operating cost, split fairly between participating fleets.</li><li><strong>Open network matching</strong> connects verified carriers to loads, with every driver checked against the Registry and every truck monitored by the SOC.</li><li><strong>An offline-first driver app</strong> (progressive web app) handles assignments, milestone confirmation, and proof of delivery with consignee OTP, digital signature and photographs.</li></ul><h3>Measured impact</h3><p>At maturity the network is designed to eliminate 30 to 35 million empty kilometres per year, avoiding 25,000 to 30,000 tonnes of CO2e annually and building the data foundation for Scope 3 emissions reporting across FMCG supply chains.</p>',
            ],
            [
                'slug' => 'true-chain-institute',
                'name' => 'True Chain Institute',
                'short_name' => 'Institute',
                'category' => 'Education and Workforce',
                'icon' => 'institute',
                'tagline' => 'Producing the professional workforce African logistics deserves.',
                'summary' => 'An FRSC-accredited training academy producing professional truck drivers, maintenance technicians, supply chain professionals, warehouse operatives and trade facilitation specialists, with every certification written to the graduate’s TCID.',
                'website_url' => 'https://truechaininstitute.com',
                'site_status' => 'live',
                'content' => '<h2>The talent engine of the chain</h2><p>The True Chain Institute is the educational company of the group and the upstream supplier of verified talent to the entire ecosystem. The Institute holds accreditation under the Federal Road Safety Corps Driving School Standardisation Programme (DSSP), placing it on the national short list of recognised heavy goods vehicle training providers.</p><h3>Programmes</h3><ul><li>Professional truck driver training and class licensing pathways.</li><li>Vehicle maintenance technician programmes.</li><li>Supply chain and logistics professional courses.</li><li>Warehouse operations certification.</li><li>Customs and trade facilitation specialist training for AfCFTA-era commerce.</li></ul><h3>Credentials that travel</h3><p>Every completed course writes a verifiable completion record to the graduate’s True Chain ID on the Registry, so a driver’s qualifications, recertifications and continuing professional development are visible to any current or future employer at the moment of hiring.</p><h3>Data-driven retraining</h3><p>The SOC’s AI engine identifies behavioural patterns in live telemetry and recommends targeted modules, fatigue management, defensive driving, fuel discipline, creating a continuous improvement loop between the road and the classroom.</p>',
            ],
            [
                'slug' => 'truck-transit-park',
                'name' => 'True Chain Truck Transit Park',
                'short_name' => 'Transit Park',
                'category' => 'Corridor Infrastructure',
                'icon' => 'park',
                'tagline' => 'Secure rest, refuelling and maintenance along every major corridor.',
                'summary' => 'A federation-wide network of secure parking, multi-temperature warehousing, driver welfare facilities, vehicle workshops and LCNG retail stations on the principal long-haul corridors.',
                'website_url' => '#',
                'site_status' => 'coming-soon',
                'content' => '<h2>Hard infrastructure for hard corridors</h2><p>Long-haul drivers in Nigeria have historically had nowhere safe to stop. The Truck Transit Park network changes that with purpose-built facilities across the six geopolitical zones.</p><h3>Each park provides</h3><ul><li><strong>Secure parking</strong> with geofence verification through the SOC.</li><li><strong>Multi-temperature warehousing</strong> for staging and cross-docking, including cold chain.</li><li><strong>Driver welfare</strong>: rest facilities, catering, sanitation and basic clinic provision.</li><li><strong>Vehicle workshops</strong> anchoring scheduled maintenance discipline and breakdown response.</li><li><strong>LCNG retail stations</strong> anchoring the heavy-duty fleet transition to cleaner natural gas.</li></ul><h3>Part of one chain</h3><p>Parks are not standalone real estate: SOC-monitored trucks rest, refuel and reload under geofence verification, drivers log structured rest cadences, and workshops anchor the group’s vehicle reliability programme.</p>',
            ],
            [
                'slug' => 'medessy-enterprises',
                'name' => 'Medessy Enterprises Limited',
                'short_name' => 'Medessy',
                'category' => 'Heritage Operations',
                'icon' => 'truck',
                'tagline' => 'The 3PL operating heritage the group is built on.',
                'summary' => 'A Nigerian indigenous logistics company with over 11 years of contracted carriage for FMCG multinationals, operating 220+ vehicles across 25+ locations, and the promoter of the True Chain program.',
                'website_url' => 'https://medessy.com',
                'site_status' => 'live',
                'content' => '<h2>Where the chain began</h2><p>Medessy Enterprises Limited is the operating heritage of the group: an indigenous third-party logistics company that has provided trucking and supply chain services to multinational FMCG clients in Nigeria for more than a decade.</p><h3>Operating platform</h3><ul><li>A managed fleet of over 220 vehicles across more than 25 operational locations.</li><li>Long-standing accounts with FMCG multinationals including Nigerian Bottling Company (Coca-Cola), International Breweries (AB InBev) and Chi Limited.</li><li>A back-office operations team, a telematics control room and disciplined fuel, maintenance and driver management.</li><li>Institutional credit relationships, including structured facilities for fleet expansion of more than 85 trucks.</li></ul><h3>Why it matters to the group</h3><p>Medessy is the proof of operating command: the anchor fleet, the first deployment base for every True Chain platform, and the real-world data set that lets the group build technology for how Nigerian road freight actually works.</p>',
            ],
        ];
    }

    /* ==================================================== pages */
    private static function pages(): array
    {
        $about = '<p class="lead">True Chain Infrastructure Company (TCIC) is the Nigerian holding company under which a portfolio of operationally interconnected, legally and financially separable companies is being built to remove the binding constraints of road freight logistics in Africa’s largest economy.</p>'
            . '<h2>Why we exist</h2>'
            . '<p>Nigerian road freight carries the overwhelming majority of the nation’s goods, yet the industry has been held back by six binding constraints: cargo insecurity, fragmentation of the carrier market, the recycling of rogue drivers between unsuspecting employers, a chronic skills deficit, the absence of corridor infrastructure, and an urgent energy and emissions imperative. No single operator can solve these alone. They require industry-level infrastructure, and that is what we build.</p>'
            . '<h2>Our companies</h2>'
            . '<p><strong>True Chain Technologies</strong> is the group’s operating technology company, delivering three deeply integrated services: the True Chain Registry for driver vetting and integrity, the True Chain Security Operations Centre for telematics, monitoring and rescue, and the True Chain Collaborative Logistics Network for shared freight coordination.</p>'
            . '<p><strong>True Chain Institute</strong> is the group’s educational company: an FRSC-accredited academy producing professional drivers, technicians and supply chain talent, with every certification written to the graduate’s permanent True Chain ID.</p>'
            . '<p><strong>True Chain Truck Transit Park</strong> is the group’s corridor infrastructure company: secure parking, warehousing, driver welfare, workshops and LCNG refuelling across the principal long-haul corridors.</p>'
            . '<p><strong>Medessy Enterprises</strong>, the group’s heritage operation, is an indigenous 3PL that has served FMCG multinationals for over 11 years with a fleet of more than 220 vehicles, and is the promoter of the group’s development program.</p>'
            . '<h2>One integrated chain</h2>'
            . '<p>The group’s defining design choice is integration. A driver is trained at the Institute, verified on the Registry, monitored by the SOC, coordinated by the CLN and supported at the Transit Parks, and one record, the True Chain ID, follows that driver through every link. Trip telemetry strengthens the integrity record; behavioural analytics route drivers back to targeted training; coordinated trips run only on verified drivers and monitored trucks. Each company is stronger because the others exist.</p>'
            . '<h2>Mission</h2>'
            . '<p>To build the trusted infrastructure, digital, human and physical, that makes African road freight safe, efficient and globally competitive.</p>'
            . '<h2>Vision</h2>'
            . '<p>A continent where every shipment moves on a true chain of verified people, secured assets and connected corridors.</p>'
            . '<h2>Our values</h2>'
            . '<ul>'
            . '<li><strong>Integrity first.</strong> We build systems where trust is evidence-based and verifiable, starting with our own conduct.</li>'
            . '<li><strong>Safety as standard.</strong> Every design decision begins with the safety of drivers, cargo and communities.</li>'
            . '<li><strong>Built to integrate.</strong> Our companies are designed as one chain; collaboration is our architecture, not an afterthought.</li>'
            . '<li><strong>Operate, then automate.</strong> Our technology is built from real operating experience, not assumptions.</li>'
            . '<li><strong>Impact that is audited.</strong> Jobs, safety, emissions: if we claim it, we measure it.</li>'
            . '</ul>'
            . '<h2>Governance</h2>'
            . '<p>The group is moving from owner-managed governance to a board-supervised architecture designed to satisfy the requirements of institutional investors and development finance partners: an independent non-executive chair, independent directors, and dedicated Audit and Risk, Nomination and Remuneration, and ESG and Impact committees, supported by a formal board charter, code of conduct, whistle-blower policy and anti-bribery framework.</p>'
            . '<h2>Impact</h2>'
            . '<p>Our work is structured against the United Nations Sustainable Development Goals, with measurable indicators for road safety (SDG 3), decent work and workforce formalisation (SDG 8), resilient infrastructure and innovation (SDG 9), and climate action through fleet fuel transition and the elimination of empty kilometres (SDG 13).</p>';

        $privacy = '<p class="lead">This Privacy Policy explains how True Chain Infrastructure Company (“TCIC”, “we”, “us”) collects, uses, stores and protects personal data when you visit this website or contact us through it. We are committed to handling personal data in accordance with the Nigeria Data Protection Act 2023 (NDPA) and the regulations and guidance issued by the Nigeria Data Protection Commission (NDPC).</p>'
            . '<h2>1. Who we are</h2>'
            . '<p>True Chain Infrastructure Company is a Nigerian holding company whose group companies provide logistics technology, training and corridor infrastructure services. For the purposes of the NDPA, TCIC is the data controller for personal data collected through this website. Our contact details are set out in Section 11.</p>'
            . '<h2>2. The data we collect</h2>'
            . '<p>We collect personal data in two ways:</p>'
            . '<ul>'
            . '<li><strong>Data you give us.</strong> When you use our contact form we collect your name, email address, and any phone number, company name, subject and message content you choose to provide.</li>'
            . '<li><strong>Technical data collected automatically.</strong> Like most websites, our hosting environment records standard server logs, including your IP address, browser type, the pages you request and the time of each request. We use this data for security monitoring, abuse prevention and to keep the website working reliably.</li>'
            . '</ul>'
            . '<p>This website does not require you to create an account, and we do not collect special categories of personal data through it.</p>'
            . '<h2>3. Why we process your data and our lawful basis</h2>'
            . '<ul>'
            . '<li><strong>Responding to your enquiries.</strong> We process contact form data on the basis of legitimate interest and, where your enquiry relates to entering a contract, steps taken at your request prior to a contract.</li>'
            . '<li><strong>Security and abuse prevention.</strong> We process technical log data on the basis of legitimate interest in protecting our systems and users.</li>'
            . '<li><strong>Legal compliance.</strong> We may process and retain data where required by applicable law.</li>'
            . '</ul>'
            . '<h2>4. Cookies</h2>'
            . '<p>This website uses only cookies that are strictly necessary for it to function, such as a session cookie used for security purposes (including protection of forms against cross-site request forgery). These essential cookies do not track you across other websites. If analytics tools are introduced in the future, this policy and our cookie practice will be updated and, where required, your consent will be requested first.</p>'
            . '<h2>5. How long we keep data</h2>'
            . '<p>Contact form messages are retained for as long as needed to handle your enquiry and for a reasonable period afterwards for record-keeping, after which they are deleted. Server logs are retained for a limited period for security purposes and then rotated.</p>'
            . '<h2>6. Who we share data with</h2>'
            . '<p>We do not sell personal data. We share personal data only with: (a) our hosting and email service providers, acting on our instructions; (b) group companies, where your enquiry relates to their services and routing it to them is necessary to respond to you; and (c) public authorities where disclosure is required by law.</p>'
            . '<h2>7. International transfers</h2>'
            . '<p>Our website may be hosted on infrastructure located outside Nigeria. Where personal data is transferred outside Nigeria, we take steps to ensure an adequate level of protection consistent with the NDPA and NDPC guidance.</p>'
            . '<h2>8. Security</h2>'
            . '<p>We apply appropriate technical and organisational measures, including encrypted transport (HTTPS), hardened administrative access, input validation and rate limiting, and restriction of access to personal data to authorised personnel only.</p>'
            . '<h2>9. Your rights</h2>'
            . '<p>Subject to the NDPA, you have the right to: access the personal data we hold about you; request correction of inaccurate data; request deletion; object to or request restriction of processing; withdraw any consent you have given; and request data portability where applicable. To exercise any of these rights, contact us using the details in Section 11. You also have the right to lodge a complaint with the Nigeria Data Protection Commission.</p>'
            . '<h2>10. Children</h2>'
            . '<p>This website is not directed at children and we do not knowingly collect personal data from children through it.</p>'
            . '<h2>11. Contact us</h2>'
            . '<p>Questions, requests or complaints about this policy or our handling of personal data should be addressed to our data protection contact via the details published on our <a href="contact">contact page</a>, marked for the attention of the Data Protection Officer.</p>'
            . '<h2>12. Changes to this policy</h2>'
            . '<p>We may update this policy from time to time. The current version will always be published on this page with the date it took effect.</p>'
            . '<p><em>Effective date: ' . date('j F Y') . '</em></p>';

        $terms = '<p class="lead">These Terms of Use govern your access to and use of this website, operated by True Chain Infrastructure Company (“TCIC”, “we”, “us”). By using this website you agree to these terms.</p>'
            . '<h2>1. About this website</h2>'
            . '<p>This website provides corporate information about TCIC and its group companies, including True Chain Technologies (the True Chain Registry, the True Chain Security Operations Centre and the True Chain Collaborative Logistics Network), True Chain Institute, True Chain Truck Transit Park and Medessy Enterprises Limited. It is an informational site; the operational services of group companies are provided under their own terms on their own platforms.</p>'
            . '<h2>2. Acceptable use</h2>'
            . '<p>You agree not to misuse this website, including by attempting unauthorised access to any part of it or its infrastructure, probing or testing its security, transmitting malware, scraping at scale, or using the contact form to send unlawful, deceptive or abusive content.</p>'
            . '<h2>3. Intellectual property</h2>'
            . '<p>All content on this website, including text, graphics, logos, the True Chain name and shield device, and underlying software, is the property of TCIC or its licensors and is protected by applicable intellectual property laws. You may view and print content for your own informational use; any other reproduction or use requires our prior written consent.</p>'
            . '<h2>4. Information, not advice</h2>'
            . '<p>Content on this website is provided for general information. It does not constitute an offer, investment advice, legal advice or a binding commitment, and forward-looking statements about plans or programs may change without notice.</p>'
            . '<h2>5. Links to other websites</h2>'
            . '<p>This website links to websites operated by group companies and may link to third-party websites. Those sites have their own terms and privacy policies, and we are not responsible for their content.</p>'
            . '<h2>6. Availability and changes</h2>'
            . '<p>We may modify, suspend or discontinue any part of this website at any time. We do not warrant that the website will be uninterrupted or error-free.</p>'
            . '<h2>7. Limitation of liability</h2>'
            . '<p>To the maximum extent permitted by law, TCIC will not be liable for any indirect, incidental or consequential loss arising from your use of, or inability to use, this website. Nothing in these terms excludes liability that cannot be excluded under applicable law.</p>'
            . '<h2>8. Privacy</h2>'
            . '<p>Our handling of personal data is described in our <a href="privacy-policy">Privacy Policy</a>, which forms part of these terms.</p>'
            . '<h2>9. Governing law</h2>'
            . '<p>These terms are governed by the laws of the Federal Republic of Nigeria, and the courts of Nigeria have jurisdiction over any dispute arising from them.</p>'
            . '<h2>10. Contact</h2>'
            . '<p>Questions about these terms may be sent to us via the <a href="contact">contact page</a>.</p>'
            . '<p><em>Effective date: ' . date('j F Y') . '</em></p>';

        return [
            [
                'slug' => 'about', 'title' => 'About the Group', 'nav_label' => 'About',
                'content' => $about,
                'meta_title' => 'About True Chain Infrastructure Company',
                'meta_description' => 'TCIC is the Nigerian holding group behind True Chain Technologies, True Chain Institute and Truck Transit Park: one integrated ecosystem for safe, efficient road freight.',
                'status' => 'published', 'show_in_nav' => 1, 'nav_order' => 10, 'is_system' => 1,
            ],
            [
                'slug' => 'privacy-policy', 'title' => 'Privacy Policy', 'nav_label' => 'Privacy Policy',
                'content' => $privacy,
                'meta_title' => 'Privacy Policy | True Chain Infrastructure Company',
                'meta_description' => 'How True Chain Infrastructure Company collects, uses and protects personal data, in line with the Nigeria Data Protection Act 2023.',
                'status' => 'published', 'show_in_nav' => 0, 'nav_order' => 90, 'is_system' => 1,
            ],
            [
                'slug' => 'terms-of-use', 'title' => 'Terms of Use', 'nav_label' => 'Terms of Use',
                'content' => $terms,
                'meta_title' => 'Terms of Use | True Chain Infrastructure Company',
                'meta_description' => 'The terms governing use of the True Chain Infrastructure Company website.',
                'status' => 'published', 'show_in_nav' => 0, 'nav_order' => 91, 'is_system' => 1,
            ],
        ];
    }
}
