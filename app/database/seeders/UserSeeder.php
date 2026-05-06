<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Curated categories: factual names and descriptions (politics, Islam, technology).
     *
     * @return list<array{theme: string, name: string, description: string}>
     */
    private static function categoryDefinitions(): array
    {
        return [
            // Politics & governance
            ['theme' => 'politics', 'name' => 'Constitutional law & rights', 'description' => 'How constitutions limit power, protect rights, and structure courts and amendments.'],
            ['theme' => 'politics', 'name' => 'Electoral systems compared', 'description' => 'Proportional representation, majoritarian models, turnout, and boundary commissions.'],
            ['theme' => 'politics', 'name' => 'EU institutions', 'description' => 'Commission, Council, Parliament, and how ordinary laws pass through co-decision.'],
            ['theme' => 'politics', 'name' => 'UN & multilateral diplomacy', 'description' => 'General Assembly, agencies, peacekeeping mandates, and treaty negotiations.'],
            ['theme' => 'politics', 'name' => 'Fiscal policy & budgets', 'description' => 'Taxation, debt ceilings, counter-cyclical spending, and transparency in public accounts.'],
            ['theme' => 'politics', 'name' => 'Tunisia: civic transition', 'description' => 'Timeline from 2011 protests through constitution-making, elections, and civil society watchdogs.'],
            ['theme' => 'politics', 'name' => 'International humanitarian law', 'description' => 'Geneva Conventions, distinction/proportionality, and ICRC’s protective mandate.'],
            ['theme' => 'politics', 'name' => 'Press freedom & misinformation', 'description' => 'Safety of journalists, platform policies, fact-checking networks, and right-of-reply norms.'],
            ['theme' => 'politics', 'name' => 'Digital rights & surveillance', 'description' => 'Encryption, lawful access debates, data retention, and cross-border evidence requests.'],
            ['theme' => 'politics', 'name' => 'Climate & COP process', 'description' => 'NDCs, loss and damage funds, transparency frameworks, and technology transfer clauses.'],
            ['theme' => 'politics', 'name' => 'Trade & tariffs', 'description' => 'WTO dispute settlement, regional FTAs, rules of origin, and supply-chain resilience.'],
            ['theme' => 'politics', 'name' => 'Public health governance', 'description' => 'WHO coordination, epidemic preparedness, vaccine equity, and national health agencies.'],
            ['theme' => 'politics', 'name' => 'Federalism & local government', 'description' => 'Revenue sharing, mayors’ powers, metropolitan planning, and subsidiarity.'],
            ['theme' => 'politics', 'name' => 'Anti-corruption frameworks', 'description' => 'UNCAC pillars, asset recovery, beneficial ownership registers, and independent prosecutors.'],
            ['theme' => 'politics', 'name' => 'Migration & asylum law', 'description' => '1951 Refugee Convention, non-refoulement, resettlement quotas, and integration policy.'],

            // Islam: scholarly & devotional topics (neutral framing)
            ['theme' => 'islam', 'name' => 'Five pillars overview', 'description' => 'Shahada, prayer, zakat, fasting, and hajj—what each pillar means in daily life.'],
            ['theme' => 'islam', 'name' => 'Ramadan & fasting', 'description' => 'Timing of fast, exemptions, tarawih, and community support during the month.'],
            ['theme' => 'islam', 'name' => 'Zakat & sadaqa', 'description' => 'Nisab, eligible recipients (asnaf), and modern zakat institutions.'],
            ['theme' => 'islam', 'name' => 'Hajj & Umrah rites', 'description' => 'Ihram, tawaf, sa’i, standing at Arafat, and practical preparation for pilgrims.'],
            ['theme' => 'islam', 'name' => 'Quranic sciences', 'description' => 'Makki/Madani revelation, themes of surahs, and reliable tafsir reading habits.'],
            ['theme' => 'islam', 'name' => 'Hadith methodology', 'description' => 'Isnad, matn, sahih/hasan/daif categories, and major hadith collections.'],
            ['theme' => 'islam', 'name' => 'Fiqh of worship', 'description' => 'Purification, prayer times, congregational prayer, and common school differences respectfully.'],
            ['theme' => 'islam', 'name' => 'Islamic finance basics', 'description' => 'Riba avoidance, asset-backed contracts, and sharia boards’ oversight role.'],
            ['theme' => 'islam', 'name' => 'Islamic history: early community', 'description' => 'Medina charter, plural communities, and preservation of knowledge in manuscripts.'],
            ['theme' => 'islam', 'name' => 'Adab & daily ethics', 'description' => 'Truthfulness, neighbour rights, cleanliness, and good speech in disagreement.'],
            ['theme' => 'islam', 'name' => 'Islamic calendar & moonsighting', 'description' => 'Lunar months, Eid determination, and global coordination among communities.'],
            ['theme' => 'islam', 'name' => 'Du’a & remembrance', 'description' => 'Morning/evening adhkar, etiquette of asking Allah, and consistency over quantity.'],
            ['theme' => 'islam', 'name' => 'Family law introduction', 'description' => 'Marriage contracts, mahr, kindness in divorce procedures—always follow qualified scholars locally.'],
            ['theme' => 'islam', 'name' => 'Science & Islam', 'description' => 'Historical contributions in medicine/astronomy and ethics of research today.'],
            ['theme' => 'islam', 'name' => 'Da’wah & dialogue', 'description' => 'Clarity, patience, and avoiding coercion; engaging questions from neighbours or colleagues.'],

            // Technology
            ['theme' => 'tech', 'name' => 'Internet architecture', 'description' => 'DNS, TLS, CDNs, and how packets route from client to edge to origin.'],
            ['theme' => 'tech', 'name' => 'Linux & systems programming', 'description' => 'Processes, file descriptors, cgroups, and observability with eBPF.'],
            ['theme' => 'tech', 'name' => 'Git & collaborative workflows', 'description' => 'Branching models, merge vs rebase, code review, and signed commits.'],
            ['theme' => 'tech', 'name' => 'Databases & SQL performance', 'description' => 'Indexing, explain plans, isolation levels, and migration discipline.'],
            ['theme' => 'tech', 'name' => 'Distributed systems', 'description' => 'CAP tradeoffs, consensus (Raft), idempotency keys, and backpressure.'],
            ['theme' => 'tech', 'name' => 'Kubernetes & containers', 'description' => 'Pods, services, ingress, HPA, and security contexts for workloads.'],
            ['theme' => 'tech', 'name' => 'OWASP & app security', 'description' => 'Injection, broken access control, SSRF, and secure SDLC checkpoints.'],
            ['theme' => 'tech', 'name' => 'Cryptography essentials', 'description' => 'AES-GCM, ECDH, certificate chains, and post-quantum migration planning.'],
            ['theme' => 'tech', 'name' => 'Machine learning practice', 'description' => 'Train/val/test splits, leakage, calibration, and responsible deployment reviews.'],
            ['theme' => 'tech', 'name' => 'Large language models', 'description' => 'Transformers, RLHF, context windows, and grounding with retrieval.'],
            ['theme' => 'tech', 'name' => 'Rust & memory safety', 'description' => 'Ownership, lifetimes, unsafe boundaries, and FFI with C libraries.'],
            ['theme' => 'tech', 'name' => 'Web platform: modern frontends', 'description' => 'Hydration, islands, accessibility, and performance budgets.'],
            ['theme' => 'tech', 'name' => 'API design: REST & GraphQL', 'description' => 'Versioning, pagination, N+1 pitfalls, and schema evolution.'],
            ['theme' => 'tech', 'name' => 'Observability stacks', 'description' => 'Structured logs, metrics, traces, SLOs, and incident retrospectives.'],
            ['theme' => 'tech', 'name' => 'Open source licensing', 'description' => 'Copyleft vs permissive, SPDX IDs, and contributor agreements.'],
        ];
    }

    /**
     * @return list<array{title: string, text: string}>
     */
    private static function publicationPool(string $theme): array
    {
        return match ($theme) {
            'politics' => [
                ['title' => 'Why separation of powers still matters', 'text' => 'Montesquieu’s insight was empirical: concentrating legislative, executive, and judicial authority invites abuse. Modern constitutions split powers differently, but the goal remains credible checks. Courts need independence, legislatures need oversight tools, and executives need lawful limits.'],
                ['title' => 'How proportional representation changes coalition math', 'text' => 'In list-PR systems, small parties often enter parliament, so governments form through negotiation rather than a single-party sweep. That can improve representation but lengthen coalition talks. Voters trade clarity of “who governs” for diversity of voices in the chamber.'],
                ['title' => 'What the European Commission actually proposes', 'text' => 'The Commission drafts EU legislation and enforces competition rules, but the Council and Parliament amend and adopt most laws. Understanding “initiative vs adoption” prevents confusion when reading Brussels headlines.'],
                ['title' => 'UN peacekeeping: mandate, limits, and consent', 'text' => 'Blue helmets deploy under Security Council mandates with host-state consent (with rare Chapter VII exceptions). They are not a world army; logistics, rules of engagement, and troop-contributing countries shape outcomes on the ground.'],
                ['title' => 'Reading a national budget like an analyst', 'text' => 'Start with revenue composition (tax vs non-tax), debt service, and capital vs current spending. Multi-year frameworks matter more than single-year headlines because many programs phase in slowly.'],
                ['title' => 'Tunisia’s civil society after 2011', 'text' => 'Associations, unions, and election-monitoring groups expanded quickly after the uprising. Their reporting on transparency and rights shaped public debate even when politics polarized. Long-term impact depends on funding, legal protections, and civic education.'],
                ['title' => 'IHL basics: civilians and combatants', 'text' => 'International humanitarian law distinguishes fighters from civilians and requires proportionate attacks. Violations are prosecuted domestically or internationally where jurisdiction exists; prevention also relies on military training and discipline.'],
                ['title' => 'Misinformation vs disagreement', 'text' => 'Healthy politics includes contested facts, but fabricated evidence and coordinated inauthentic behavior erode trust. Responses combine media literacy, platform integrity teams, and open data so citizens can verify claims.'],
                ['title' => 'End-to-end encryption and lawful access', 'text' => 'Strong encryption protects journalists and dissidents, yet investigators argue for exceptional access. Engineers note that any “golden key” weakens security for everyone; policy debates weigh crime prevention against systemic risk.'],
                ['title' => 'NDCs and the Paris Agreement rhythm', 'text' => 'Countries submit nationally determined contributions on five-year cycles, aiming to tighten ambition over time. Transparency rules require reporting so peers can compare progress, not just promises.'],
                ['title' => 'Tariffs as a signal—and a cost', 'text' => 'Tariffs can protect nascent industries but raise consumer prices and invite retaliation. Economists often prefer targeted industrial policy with clear sunset clauses and metrics.'],
                ['title' => 'WHO’s role is coordination, not sovereignty', 'text' => 'The World Health Organization sets norms and coordinates outbreaks, but member states implement law and spend budgets. Pandemic treaties debate equity in vaccine access and pathogen sharing.'],
                ['title' => 'Federalism: when cities outgrow old maps', 'text' => 'Metro regions need transport and housing coordination across municipalities. Fiscal transfers and special districts are common fixes when boundaries no longer match economic life.'],
                ['title' => 'Beneficial ownership registers', 'text' => 'Knowing who ultimately owns companies reduces money laundering and conflict-of-interest risks. Effective registers require verification, penalties for false filings, and cross-border data sharing.'],
                ['title' => 'Non-refoulement in asylum law', 'text' => 'The core protection is not returning people to places where they face persecution or torture. Implementation depends on fair asylum procedures and safe third-country agreements that meet human-rights standards.'],
            ],
            'islam' => [
                ['title' => 'Shahada as intention and truthfulness', 'text' => 'The testimony of faith is not only words but alignment of the heart and conduct with monotheism. Scholars emphasize sincerity (ikhlas) and learning what negates tawhid so the declaration remains meaningful daily.'],
                ['title' => 'Ramadan: more than skipping lunch', 'text' => 'Fasting teaches restraint of tongue and glance, not only food. The pre-dawn meal (suhur) is encouraged; breaking fast at sunset follows the prophetic practice. Travelers and the ill have concessions documented in classical fiqh.'],
                ['title' => 'Zakat purifies surplus wealth', 'text' => 'At 2.5% on qualifying zakatable assets above nisab, zakat moves wealth to eight categories of recipients named in Quran 9:60. Many Muslims use zakat organizations for auditing and local distribution.'],
                ['title' => 'Hajj in brief: unity and patience', 'text' => 'Pilgrims enter ihram, perform tawaf around the Kaaba, walk between Safa and Marwa, stand at Arafat, then complete stoning and sacrifice rites per guidance. Crowds require patience; scholars publish step-by-step checklists for first-timers.'],
                ['title' => 'Reading tafsir responsibly', 'text' => 'Start with reputable scholars and editions that cite context (asbab al-nuzul) without isolating verses. Comparing respectful interpretations deepens understanding more than cherry-picking translations online.'],
                ['title' => 'Hadith: isnad as intellectual heritage', 'text' => 'The chain of narration was scrutinized centuries before modern historiography. Terms like sahih and hasan describe transmission strength; lay readers benefit from curated collections and teacher guidance.'],
                ['title' => 'Wudu: spiritual and hygienic dimensions', 'text' => 'Ablution removes minor ritual impurity and prepares the mind for prayer. Classical manuals list obligations (fard) vs recommended acts; differences between schools are well documented and should be learned from qualified teachers.'],
                ['title' => 'Islamic finance: asset-backed contracts', 'text' => 'Murabaha marks up cost-plus sales with disclosed profit; ijara leases assets; musharaka shares risk and return. AAOIFI standards help harmonize sharia board rulings across banks.'],
                ['title' => 'Medina: pluralism under charter', 'text' => 'The Constitution of Medina outlined mutual defense and rights among tribes and faith communities. Historians study it as an early governance document reflecting social contracts in seventh-century Arabia.'],
                ['title' => 'Adab in disagreement online', 'text' => 'Islamic ethics urge lowering the wing of humility, avoiding mockery, and verifying news before sharing. Debates on fiqh should preserve brotherhood/sisterhood even when rulings differ.'],
                ['title' => 'Lunar months and global communities', 'text' => 'Islamic months begin with moonsighting or calculated calendars depending on community methodology. Respectful coexistence means not belittling others’ legitimate juristic choices where differences are classical.'],
                ['title' => 'Morning adhkar: small habits, large barakah', 'text' => 'Short Quranic and prophetic supplications after Fajr frame the day with remembrance. Consistency beats volume; many apps now offer authenticated wordings with sources.'],
                ['title' => 'Marriage contracts: clarity and kindness', 'text' => 'A written contract records mahr, rights, and agreed terms transparently. Scholars stress kindness in speech and fair treatment; local law still governs civil registration requirements.'],
                ['title' => 'Astronomy in the Islamic golden age', 'text' => 'Observatories in Baghdad, Maragheh, and Samarkand refined planetary models and star catalogs. That legacy informs today’s conversations about science education in Muslim-majority countries.'],
                ['title' => 'Answering sincere questions about Islam', 'text' => 'Good da’wah listens first, cites authentic sources, and admits “I don’t know” when appropriate. The Quran encourages calling with wisdom and fair exhortation, not coercion.'],
            ],
            'tech' => [
                ['title' => 'What happens when you type a URL', 'text' => 'The browser resolves DNS, opens a TCP connection, negotiates TLS, sends an HTTP request, and renders HTML/CSS/JS incrementally. CDNs cache static assets closer to users to cut latency and origin load.'],
                ['title' => 'Linux capabilities vs running as root', 'text' => 'Capabilities slice root privileges into granular permissions for containers. Combined with seccomp and AppArmor/SELinux, they reduce blast radius when a service is compromised.'],
                ['title' => 'Git rebase: cleaner history, sharper edges', 'text' => 'Interactive rebase tidies commits before review, but rewriting shared branches disrupts teammates. Rule of thumb: rebase local branches; merge long-lived shared lines unless your team standardizes otherwise.'],
                ['title' => 'Why your index might not help', 'text' => 'Query planners ignore indexes on low-cardinality columns or when functions wrap indexed fields. EXPLAIN ANALYZE shows actual timings; partial indexes can target hot predicates.'],
                ['title' => 'Raft: leader, followers, and quorum', 'text' => 'Raft elects a leader for log replication; committed entries survive (N/2)+1 failures. etcd and Consul embed Raft for strongly consistent metadata.'],
                ['title' => 'Kubernetes probes save rollouts', 'text' => 'Liveness restarts stuck containers; readiness removes them from Service endpoints until dependencies recover. Misconfigured probes cause flapping or silent traffic blackholes.'],
                ['title' => 'OWASP Top 10: access control first', 'text' => 'Broken access control often tops the list because frameworks cannot guess your authorization model. Centralize policy checks, test object-level permissions, and log denials.'],
                ['title' => 'TLS 1.3 trimmed the handshake', 'text' => 'Fewer round trips and mandatory forward secrecy improve performance and security. Operators must still manage certificate rotation and HSTS preload carefully.'],
                ['title' => 'Evaluating ML models beyond accuracy', 'text' => 'Precision/recall tradeoffs, calibration curves, and subgroup metrics reveal silent failures. Production needs monitoring for data drift and concept drift, not a one-time Kaggle score.'],
                ['title' => 'Transformers and attention', 'text' => 'Self-attention maps relationships between tokens in parallel, enabling long-range dependencies compared to RNNs. Scale plus data produced the LLM era; efficiency now drives sparse attention and quantization research.'],
                ['title' => 'Rust borrow checker in one paragraph', 'text' => 'Each value has one owner; borrows are either many readers or one writer at a time. Lifetimes annotate how long references are valid, preventing dangling pointers without a GC.'],
                ['title' => 'Hydration costs in SPA frameworks', 'text' => 'Shipping large client bundles delays interactivity. Server components, streaming SSR, and islands architectures reduce JavaScript sent to the browser for mostly-static pages.'],
                ['title' => 'GraphQL N+1 and DataLoader', 'text' => 'Resolvers per field invite repeated database queries. Batching with DataLoader or declarative joins in the resolver layer keeps latency predictable.'],
                ['title' => 'Three pillars of observability', 'text' => 'Logs tell stories, metrics aggregate health, traces show causality across services. SLOs tie them to user-visible reliability budgets and error budgets for feature velocity.'],
                ['title' => 'GPL vs MIT in one decision frame', 'text' => 'GPL copyleft requires derivative works to stay open; MIT/BSD maximize reuse in proprietary stacks. Choose based on community goals, patent clauses, and compliance bandwidth.'],
            ],
            default => [],
        };
    }

    /**
     * @return list<string>
     */
    private static function commentPool(string $theme): array
    {
        return match ($theme) {
            'politics' => [
                'Worth comparing with how Germany handles surplus seats in Bundestag calculations.',
                'The transparency portal for that ministry publishes machine-readable budgets—useful for fact-checks.',
                'Small correction: the Commission proposes; Parliament and Council adopt in ordinary legislative procedure.',
                'On IHL, the ICRC’s customary law study is a dense but authoritative reference.',
                'For climate, check whether the NDC target is unconditional or conditional on finance.',
                'Federal countries often need equalization grants so poorer regions can fund schools.',
                'Asylum timelines vary wildly; procedural fairness matters as much as the headline policy.',
                'Coalition agreements in Nordics are public PDFs—good models for accountability.',
            ],
            'islam' => [
                'JazakAllahu khayran—this matches what our local imam emphasized about adab in disputes.',
                'For fiqh specifics, I follow my madhhab’s manuals; global fatwas can miss local context.',
                'Small note: moonsighting vs calculation is a valid ikhtilaf; respect both where possible.',
                'Zakat on retirement accounts is nuanced—worth asking a qualified accountant + scholar.',
                'Tafsir sessions at our masjid started with short Arabic vocabulary—helped beginners a lot.',
                'The hadith science intro by ibn al-Salah is still a classic roadmap for students.',
                'For Hajj logistics, Saudi Nusuk guidance plus a veteran group leader is the practical combo.',
                'Remember intention (niyyah) before acts of worship—scholars tie it to validity in many schools.',
            ],
            'tech' => [
                'If you run EXPLAIN (ANALYZE, BUFFERS) you’ll see whether seq scans dominate.',
                'We fixed a similar outage by tightening readiness probes and adding startup dependencies.',
                'For TLS, cert-manager + DNS-01 avoids HTTP-01 challenges on private clusters.',
                'Rust FFI: bindgen + cxx crate reduced our unsafe surface a lot.',
                'Consider canary deploys with automatic rollback on SLO burn rate alerts.',
                'We migrated GraphQL resolvers to DataLoader and cut p99 latency nearly in half.',
                'OWASP ASVS is a solid checklist if you need procurement-friendly security requirements.',
                'On LLMs: retrieval grounding cut hallucinations on our internal docs assistant.',
            ],
            default => ['Thanks for sharing this—useful context.'],
        };
    }

    public function run(): void
    {
        $now = now();

        // Smaller dataset for faster local seeding.
        $users = User::factory(24)->create([
            'password' => Hash::make('123456789'),
        ]);

        foreach ($users as $user) {
            Profile::factory()->create(['user_id' => $user->id]);
        }

        $adminUser = User::query()->firstOrCreate(
            ['email' => 'admin@ihm.local'],
            [
                'password' => Hash::make('admin123456'),
                'status' => 'active',
            ]
        );

        Profile::query()->firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'display_name' => 'admin',
                'gender' => null,
            ]
        );

        Admin::query()->firstOrCreate(['user_id' => $adminUser->id]);

        // Curated categories on politics, Islam, and technology (factual names + descriptions).
        $categoryRows = [];
        $categoryThemeById = [];
        foreach (self::categoryDefinitions() as $def) {
            $categoryId = Str::uuid()->toString();
            $categoryThemeById[$categoryId] = $def['theme'];
            $categoryRows[] = [
                'id' => $categoryId,
                'name' => $def['name'],
                'description' => $def['description'],
                'profile_image_path' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $categoryIds = array_keys($categoryThemeById);
        DB::table('categories')->insert($categoryRows);

        // Each user follows 4 categories.
        $categoryFollowerRows = [];
        foreach ($users as $user) {
            $followed = collect($categoryIds)->shuffle()->take(4);
            foreach ($followed as $categoryId) {
                $categoryFollowerRows[] = [
                    'category_id' => $categoryId,
                    'user_id' => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('category_followers')->insert($categoryFollowerRows);

        // Seed a mix of accepted friendships and pending friend requests.
        $friendPairs = [];
        $friendRequestRows = [];

        foreach ($users as $user) {
            $friendCandidates = $users
                ->where('id', '!=', $user->id)
                ->shuffle()
                ->take(rand(4, 10));

            foreach ($friendCandidates as $friend) {
                $pair = [$user->id, $friend->id];
                $normalizedPair = $pair;
                sort($normalizedPair);
                $pairKey = implode('|', $normalizedPair);

                if (isset($friendPairs[$pairKey])) {
                    continue;
                }

                $friendPairs[$pairKey] = true;
                $status = rand(1, 100) <= 65 ? 'accepted' : 'pending';

                $friendRequestRows[] = [
                    'id' => Str::uuid()->toString(),
                    'sender_id' => $pair[0],
                    'receiver_id' => $pair[1],
                    'status' => $status,
                    'responded_at' => $status === 'accepted' ? $now : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($friendRequestRows)) {
            DB::table('friend_requests')->insert($friendRequestRows);
        }

        // 2-4 publications per user: titles/bodies match category theme (politics, islam, tech).
        $publicationMeta = [];
        foreach ($users as $user) {
            $count = rand(2, 4);
            for ($i = 0; $i < $count; $i++) {
                $categoryId = $categoryIds[array_rand($categoryIds)];
                $theme = $categoryThemeById[$categoryId];
                $pool = self::publicationPool($theme);
                $article = $pool[array_rand($pool)];

                $id = Str::uuid()->toString();
                $mediaRoll = rand(1, 100);
                $mediaTypes = ['image', 'video', 'audio'];
                $mediaType = $mediaRoll <= 30 ? $mediaTypes[array_rand($mediaTypes)] : null;

                DB::table('contents')->insert([
                    'id'         => $id,
                    'type'       => 'publication',
                    'status'     => 'visible',
                    'author_id'  => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('publications')->insert([
                    'id'          => $id,
                    'title'       => $article['title'],
                    'text'        => $article['text'],
                    'media_type'  => $mediaType,
                    'category_id' => $categoryId,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                $publicationMeta[] = ['id' => $id, 'theme' => $theme];
            }
        }

        // 1-8 comments per publication from random users (theme-aligned snippets).
        foreach ($publicationMeta as $row) {
            $pubId = $row['id'];
            $theme = $row['theme'];
            $comments = self::commentPool($theme);
            $count = rand(1, 8);
            for ($i = 0; $i < $count; $i++) {
                $id = Str::uuid()->toString();
                $text = $comments[array_rand($comments)];

                DB::table('contents')->insert([
                    'id'         => $id,
                    'type'       => 'comment',
                    'status'     => 'visible',
                    'author_id'  => $users->random()->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('comments')->insert([
                    'id'             => $id,
                    'text'           => $text,
                    'publication_id' => $pubId,
                    'parent_id'      => null,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }
        }

        if ($this->command) {
            $this->command->newLine();
            $this->command->info('Seeded usernames (use password: 123456789):');

            foreach ($users->take(4) as $sampleUser) {
                $this->command->line('- ' . ($sampleUser->profile->display_name ?? 'unknown_user'));
            }

            $this->command->newLine();
            $this->command->info('Admin login: admin / admin123456');
        }
    }
}
