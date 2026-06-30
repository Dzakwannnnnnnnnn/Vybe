<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Message;
use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── 1. Create dummy users ─────────────────────────────────────────
        $users = [
            [
                'name'            => 'Alex Rivera',
                'username'        => 'alexrivera',
                'email'           => 'alex@vybe.test',
                'password'        => Hash::make('password'),
                'bio'             => 'Photographer & traveler 📷 | Capturing life one frame at a time ✨',
                'profile_picture' => null,
            ],
            [
                'name'            => 'Mia Tanaka',
                'username'        => 'miatanaka',
                'email'           => 'mia@vybe.test',
                'password'        => Hash::make('password'),
                'bio'             => 'Designer • Coffee addict ☕ • Tokyo → Jakarta',
                'profile_picture' => null,
            ],
            [
                'name'            => 'Zaid Hassan',
                'username'        => 'zaidhassan',
                'email'           => 'zaid@vybe.test',
                'password'        => Hash::make('password'),
                'bio'             => 'Software engineer by day, musician by night 🎸 | Open source enthusiast',
                'profile_picture' => null,
            ],
            [
                'name'            => 'Luna Park',
                'username'        => 'lunapark',
                'email'           => 'luna@vybe.test',
                'password'        => Hash::make('password'),
                'bio'             => 'Mindfulness coach 🧘 | Writer | Spreading good vibes only',
                'profile_picture' => null,
            ],
            [
                'name'            => 'Kai Santos',
                'username'        => 'kaisantos',
                'email'           => 'kai@vybe.test',
                'password'        => Hash::make('password'),
                'bio'             => 'Streetwear & sneaker culture 👟 | Creative director @VybeBrand',
                'profile_picture' => null,
            ],
        ];

        $createdUsers = collect();
        foreach ($users as $userData) {
            $createdUsers->push(User::create($userData));
        }

        [$alex, $mia, $zaid, $luna, $kai] = $createdUsers->all();

        // ─── 2. Follow relationships ───────────────────────────────────────
        // Alex follows Mia, Zaid, Luna
        $alex->following()->attach([$mia->id, $zaid->id, $luna->id]);
        // Mia follows Alex, Kai
        $mia->following()->attach([$alex->id, $kai->id]);
        // Zaid follows Alex, Mia, Luna, Kai
        $zaid->following()->attach([$alex->id, $mia->id, $luna->id, $kai->id]);
        // Luna follows Kai, Zaid
        $luna->following()->attach([$kai->id, $zaid->id]);
        // Kai follows Alex, Mia
        $kai->following()->attach([$alex->id, $mia->id]);

        // ─── 3. Thread posts ───────────────────────────────────────────────
        $t1 = Post::create([
            'user_id' => $zaid->id,
            'type'    => 'thread',
            'content' => 'Hot take: the best code is the code you delete. Simplicity beats complexity every time. Fight me. 🔥',
        ]);

        $t2 = Post::create([
            'user_id' => $luna->id,
            'type'    => 'thread',
            'content' => 'Morning reminder: You don\'t have to be productive every moment of every day. Rest is part of the process. 🌿',
        ]);

        $t3 = Post::create([
            'user_id' => $alex->id,
            'type'    => 'thread',
            'content' => 'Just spent 3 hours chasing golden hour light and completely forgot to eat lunch. Worth it? Absolutely. 📸',
        ]);

        $t4 = Post::create([
            'user_id' => $kai->id,
            'type'    => 'thread',
            'content' => 'The sneaker market right now is wild. Bought a pair for resale and now I\'m too attached to sell 😂👟',
        ]);

        $t5 = Post::create([
            'user_id' => $mia->id,
            'type'    => 'thread',
            'content' => 'Design tip: white space is not wasted space. It\'s breathing room. Give your layouts room to breathe. 🎨',
        ]);

        // Thread replies
        Post::create([
            'user_id'   => $mia->id,
            'type'      => 'thread',
            'content'   => 'Agree 100%! Clean code is happy code. Less is more always.',
            'parent_id' => $t1->id,
        ]);
        Post::create([
            'user_id'   => $alex->id,
            'type'      => 'thread',
            'content'   => 'Said every senior dev who\'s been burned by "clever" solutions 😂',
            'parent_id' => $t1->id,
        ]);
        Post::create([
            'user_id'   => $zaid->id,
            'type'      => 'thread',
            'content'   => 'Wait, this actually hits different after a long week of debugging...',
            'parent_id' => $t2->id,
        ]);
        Post::create([
            'user_id'   => $kai->id,
            'type'      => 'thread',
            'content'   => 'Same energy as buying shoes then keeping them in the box forever 😅',
            'parent_id' => $t4->id,
        ]);

        // ─── 4. Feed posts (no real images, so use placeholder text only) ──
        $f1 = Post::create([
            'user_id'  => $alex->id,
            'type'     => 'feed',
            'content'  => 'Bali at sunrise never gets old 🌅 #travel #photography #bali',
            'media_path' => null,
        ]);

        $f2 = Post::create([
            'user_id'  => $mia->id,
            'type'     => 'feed',
            'content'  => 'New workspace setup ✨ minimal and clean, just how I like it. #design #workspace',
            'media_path' => null,
        ]);

        $f3 = Post::create([
            'user_id'  => $kai->id,
            'type'     => 'feed',
            'content'  => 'Latest pickup just dropped 🔥 these are a W #sneakers #streetwear',
            'media_path' => null,
        ]);

        // ─── 5. Likes ─────────────────────────────────────────────────────
        $likePairs = [
            [$mia->id, $t1->id],
            [$alex->id, $t1->id],
            [$luna->id, $t1->id],
            [$kai->id, $t2->id],
            [$mia->id, $t2->id],
            [$zaid->id, $t3->id],
            [$luna->id, $t3->id],
            [$alex->id, $t4->id],
            [$mia->id, $t4->id],
            [$zaid->id, $t5->id],
            [$kai->id, $t5->id],
            [$mia->id, $f1->id],
            [$zaid->id, $f1->id],
            [$kai->id, $f2->id],
            [$alex->id, $f2->id],
            [$luna->id, $f3->id],
        ];

        foreach ($likePairs as [$uid, $pid]) {
            Like::create(['user_id' => $uid, 'post_id' => $pid]);
        }

        // ─── 6. Comments ──────────────────────────────────────────────────
        Comment::create(['user_id' => $mia->id,  'post_id' => $t3->id, 'content' => 'The dedication 🙌 worth every missed meal']);
        Comment::create(['user_id' => $luna->id, 'post_id' => $t3->id, 'content' => 'Golden hour shots are always worth it 🌅']);
        Comment::create(['user_id' => $kai->id,  'post_id' => $t5->id, 'content' => 'This is why your designs always look so clean!']);
        Comment::create(['user_id' => $alex->id, 'post_id' => $f2->id, 'content' => 'Goals! What monitor is that? 👀']);
        Comment::create(['user_id' => $luna->id, 'post_id' => $f3->id, 'content' => 'Those are 🔥🔥🔥 where\'d you cop?']);

        // ─── 7. Direct messages ───────────────────────────────────────────
        // Alex <-> Mia
        Message::create(['sender_id' => $alex->id, 'receiver_id' => $mia->id, 'content' => 'Hey Mia! Love your latest design work 😍']);
        Message::create(['sender_id' => $mia->id,  'receiver_id' => $alex->id, 'content' => 'Thanks Alex! And omg your photos are incredible 📸']);
        Message::create(['sender_id' => $alex->id, 'receiver_id' => $mia->id, 'content' => 'We should collab sometime! I shoot, you design 🙌']);
        Message::create(['sender_id' => $mia->id,  'receiver_id' => $alex->id, 'content' => 'I\'m so in!! DM me details ✨']);

        // Zaid <-> Luna
        Message::create(['sender_id' => $zaid->id, 'receiver_id' => $luna->id, 'content' => 'Your mindfulness posts hit different at 2am while debugging 😅']);
        Message::create(['sender_id' => $luna->id, 'receiver_id' => $zaid->id, 'content' => 'Haha I\'m glad! Step away from the screen and breathe 🌿']);
        Message::create(['sender_id' => $zaid->id, 'receiver_id' => $luna->id, 'content' => 'Ok ok, committing this fix and closing my laptop']);
        Message::create(['sender_id' => $luna->id, 'receiver_id' => $zaid->id, 'content' => 'That\'s the spirit! Rest well 🌙']);

        // Kai <-> Alex
        Message::create(['sender_id' => $kai->id,  'receiver_id' => $alex->id, 'content' => 'Bro your Bali shots 🤯 you shooting on film or digital?']);
        Message::create(['sender_id' => $alex->id, 'receiver_id' => $kai->id,  'content' => 'Digital but I add a film grain overlay in post 😄']);
        Message::create(['sender_id' => $kai->id,  'receiver_id' => $alex->id, 'content' => 'Ahhh that explains everything. Fire work as always 🔥']);

        // ─── 8. Stories (expired) - These expire fast but show structure ──
        Story::create([
            'user_id'    => $luna->id,
            'media_path' => 'stories/placeholder.jpg',
            'expires_at' => now()->addHours(20),
        ]);

        Story::create([
            'user_id'    => $kai->id,
            'media_path' => 'stories/placeholder.jpg',
            'expires_at' => now()->addHours(18),
        ]);

        $this->command->info('✅ Dummy accounts created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials (password: password):');
        $this->command->info('  alex@vybe.test  → @alexrivera');
        $this->command->info('  mia@vybe.test   → @miatanaka');
        $this->command->info('  zaid@vybe.test  → @zaidhassan');
        $this->command->info('  luna@vybe.test  → @lunapark');
        $this->command->info('  kai@vybe.test   → @kaisantos');
    }
}
