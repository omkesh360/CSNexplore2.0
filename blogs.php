<?php
$page_title   = "Travel Blogs & Stories | CSNExplore";
$current_page = "blogs.php";
$extra_styles = "
    .glass-effect { background:rgba(255,255,255,0.07); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.12); }
";
require 'header.php';

$blogs = [
    ['title'=>'The Golden Thread: Weaver Stories of Paithani',  'tag'=>'Heritage',    'read'=>'5',  'date'=>'Mar 10, 2026', 'excerpt'=>'Discover the centuries-old craft of Paithani silk weaving and the artisans who keep this tradition alive in Paithan village.', 'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDDXtI36Ak_c1oZP6DBN6XuNcipOp1FZyPw7_5eZFyJqIOdw379DWRBbRRXte6mYOLJ41fORaujKPlbqKCgiAtknxK8WD15z2aap31vq9lcZa10hAxe8FtvE9ofYg-4g8RLQvsJk7-syHR6JOkr3oE5aCovzf5lWImP3Uhcg8xHdZkKXN7DRQA_s2iONPxGfPDcLRTiCA-gTbssnUVTvBcQEKndxQyNQlXmV6aeTeQgXBzimUbkiGVMrCQjYESOqk9VTJ9BqhJy4e8c'],
    ['title'=> "A Meteor's Legacy: Secrets of Lonar Crater",    'tag'=>'Nature',      'read'=>'8',  'date'=>'Mar 05, 2026', 'excerpt'=>'One of the world\'s oldest and largest meteor impact craters sits just 140 km from Sambhajinagar — and it\'s breathtaking.', 'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuAIj__CDpESRFwR6xAYYw37kdQvkHTy5seuR-Ma_bH84a9T7P27_gmPUo-lSc8KkW_fVByifLuW45uJ0WCPAiFsghRYhsEBINf579L1sYbpfXOHpquLiVizkSZ8lnurOedj5GPcQjvRgwDEUaJ3XMAwTQgE7mUwvdnWwuN3-k9e1ql1RKo2JzRH20qHLgmzSf8GSAwWseV5C-zbc4DsHmMTCesNCz_A-2pFPuATXfNKGQy8tUwmppIvnKHv42o0CleBEwGq96jju05n'],
    ['title'=>'The Royal Plate: A Mughlai Culinary Tour',       'tag'=>'Cuisine',     'read'=>'4',  'date'=>'Feb 28, 2026', 'excerpt'=>'From Naan Qalia to Shahi Tukda — explore the rich Mughlai food heritage that defines Sambhajinagar\'s dining scene.', 'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuCAwOHbzGC4iPe-e8neBtLxAN4yaUk7-QpESKj9cIbPVVUs0qCRpORyT2eUw_77wag6qNYKqXqvAAWZ7dzez2J-K0IRYRo4WPCmWDYDN60oK86sQ2759o4f6AmcwvmgdJ5IsNYYNg5aVlTBFJ9NkpxJR9MKHh1ESOJd8H451r0_gUPcb5Se1d5uZAAik3HgX5D1-nbFI-g1rTPdD5oweX1488TkQoGJu-iQqmux2quuxYsuoEtJ1dhVogUNOxdCDzZgNWASd9C1-8W1'],
    ['title'=>'Ellora After Dark: A Night at the Caves',        'tag'=>'Heritage',    'read'=>'7',  'date'=>'Feb 20, 2026', 'excerpt'=>'The light-and-sound show at Ellora transforms these ancient rock-cut temples into a living, breathing spectacle.', 'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDfTDZo8LglfsdX1vCy-PfHltcZor3jl-l4xxrXMYSU-zLgoKXxY-ouUImyR0WZq69V0y63PE1wDL2_EfqYwWhgQOHPVDJVHhyGGB7H8kZNyboNAXVxWDvlFW_Z_QRXuTKMBuuk7a9HgI3Gde3PidzWIcOhtgs4QAHX2DHA2V6QUaFo6mYDZzEhvq1Y7FwjBSsjTNmfwco23Zfvdb8laeVoTMZHDGMoMrH3yPn4aQDHZ9AJE-WXiuWGVG-c0BegSoJwB1zEXVVWIUie'],
    ['title'=>'Riding the Deccan: A Bike Trip Guide',           'tag'=>'Travel Tips', 'read'=>'6',  'date'=>'Feb 14, 2026', 'excerpt'=>'Plan the perfect motorcycle road trip from Sambhajinagar to Ajanta, Lonar, and back — with stops, routes, and tips.', 'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuBFYIfhiyxiKGSvW26EEi6qWok9NLO6cRlbBw0oCLlVCiV9F1e_mtQoiJK-2Dnb5uwU3K6b01miWgbmBQaNlDcPazf_LXbqwv3zx4f_F6Jsl627xYGPA3B5kQg_01L4gEPJseizInfQycEdL6o-IO9u7fAjGuMEnr_iPgYZShZ5e9VLbqTAdlhGFW8Tnss81gBfiFHSmzorGkalt_cF3Hi8ycEbYCGC_a4e9UyOZAQ8J4m9XHF2EcZwdaPo2OpFbnwwGvVRYdxLSxsT'],
    ['title'=>'Bibi Ka Maqbara: The Taj of the Deccan',         'tag'=>'Heritage',    'read'=>'5',  'date'=>'Feb 08, 2026', 'excerpt'=>'Built in 1660 by Azam Shah in memory of his mother, this stunning monument rivals the Taj Mahal in beauty and emotion.', 'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuA-D79qVGeL_kN4YxCqUZP2BKG-9x4p5sqXX-bx0TBbP7ZKU1EVKO-Jw5Emw0obv0VOq6md2Qzn1vqWm4o1V32OKXDNCFPuQqV_AcF7hgELzcZsE2TJ4S7h6v6mvvV08rRZxxhTTmP6woVo2zTsTcjrQSpVn_fWlQ_srrJ12DxixFiiNTPdmEfpgtbSvHMNMrYwK4BJubaMlVAZqAQJfCh5W7fpWsJ57_3zu0gTBtG7cIlIZBipNxLt3n2U-UadC2ONwF48y4nrSeOv'],
];

$featured = $blogs[0];
$rest     = array_slice($blogs, 1);
?>

<!-- Breadcrumb -->
<div class="w-full bg-slate-100 dark:bg-white/5 border-b border-slate-200 dark:border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="index.php" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-base">home</span>Home
        </a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <span class="text-slate-800 dark:text-white font-semibold">Blogs</span>
    </div>
</div>

<main class="bg-white dark:bg-[#0a0705] min-h-screen">

<!-- Featured Article Hero (full width) -->
<section class="relative rounded-none overflow-hidden group">
    <div class="h-[500px] md:h-[560px] w-full relative">
        <img class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
             src="<?php echo $featured['img']; ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>"/>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0a0705]/90 via-[#0a0705]/40 to-transparent"></div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-16 flex flex-col items-start gap-4 max-w-7xl mx-auto">
        <div class="flex items-center gap-3">
            <span class="bg-primary text-white text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full">Featured Story</span>
            <span class="text-slate-200 text-sm font-medium"><?php echo $featured['read']; ?> min read · <?php echo $featured['tag']; ?></span>
        </div>
        <h2 class="text-white text-4xl md:text-5xl font-serif font-black leading-tight max-w-3xl">
            <?php echo htmlspecialchars($featured['title']); ?>
        </h2>
        <p class="text-slate-200 text-lg max-w-2xl hidden md:block"><?php echo htmlspecialchars($featured['excerpt']); ?></p>
        <a href="blog-detail.php" class="mt-4 bg-primary hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-xl transition-all shadow-lg flex items-center gap-2">
            Read Full Story <span class="material-symbols-outlined">arrow_forward</span>
        </a>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div class="flex items-center gap-2 overflow-x-auto pb-2">
            <?php
            $cats = ['All Stories','Heritage','Cuisine','Nature','Travel Tips'];
            foreach ($cats as $i => $cat): ?>
            <button class="whitespace-nowrap px-6 py-2.5 <?php echo $i===0 ? 'bg-primary text-white' : 'bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:border-primary'; ?> rounded-full font-semibold text-sm transition-colors">
                <?php echo $cat; ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-4 text-slate-500">
            <span class="text-sm font-medium">Sort by:</span>
            <select class="bg-transparent border-none focus:ring-0 font-bold text-sm text-slate-900 dark:text-slate-100 cursor-pointer">
                <option>Latest Updates</option>
                <option>Most Popular</option>
                <option>Editor's Choice</option>
            </select>
        </div>
    </div>

    <!-- Blog Grid -->
    <h3 class="text-2xl font-serif font-black mb-8 flex items-center gap-3 dark:text-white">
        <span class="w-8 h-1 bg-primary rounded-full inline-block"></span>Latest Insights
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($rest as $blog): ?>
        <article class="flex flex-col group h-full">
            <div class="relative rounded-2xl overflow-hidden mb-5 aspect-video shadow-lg">
                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                     src="<?php echo $blog['img']; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>"/>
                <div class="absolute top-4 left-4">
                    <span class="bg-white/90 dark:bg-[#0a0705]/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-primary uppercase">
                        <?php echo $blog['tag']; ?>
                    </span>
                </div>
            </div>
            <div class="flex flex-col flex-grow">
                <div class="flex items-center gap-4 mb-3 text-slate-500 dark:text-slate-400 text-xs font-semibold">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span><?php echo $blog['read']; ?> min read</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">calendar_today</span><?php echo $blog['date']; ?></span>
                </div>
                <h4 class="text-xl font-serif font-bold leading-snug mb-3 group-hover:text-primary transition-colors dark:text-white">
                    <?php echo htmlspecialchars($blog['title']); ?>
                </h4>
                <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-6"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                <div class="mt-auto pt-4 border-t border-slate-100 dark:border-white/10 flex justify-between items-center">
                    <a class="text-primary font-bold text-sm flex items-center gap-1 group/btn" href="blog-detail.php">
                        Read More
                        <span class="material-symbols-outlined text-base transition-transform group-hover/btn:translate-x-1">chevron_right</span>
                    </a>
                    <button class="material-symbols-outlined text-slate-400 hover:text-primary transition-colors">bookmark</button>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-16 flex items-center justify-center gap-2">
        <button class="h-12 w-12 flex items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <button class="h-12 w-12 flex items-center justify-center rounded-xl bg-primary text-white font-bold">1</button>
        <button class="h-12 w-12 flex items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors font-bold dark:text-white">2</button>
        <button class="h-12 w-12 flex items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors font-bold dark:text-white">3</button>
        <button class="h-12 w-12 flex items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
            <span class="material-symbols-outlined dark:text-white">chevron_right</span>
        </button>
    </div>

    <!-- Newsletter -->
    <section class="mt-20 mb-8 p-8 md:p-12 rounded-3xl bg-primary/10 border border-primary/20 flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1">
            <h3 class="text-3xl font-serif font-black mb-4 dark:text-white">Never miss a hidden gem</h3>
            <p class="text-slate-600 dark:text-slate-400">Weekly travel inspiration, local tips, and exclusive stories from Sambhajinagar.</p>
        </div>
        <div class="flex-1 w-full max-w-md">
            <form method="POST" action="subscribe.php" class="flex flex-col sm:flex-row gap-3">
                <input type="email" name="email" required placeholder="Your email address"
                       class="flex-grow rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 focus:ring-primary focus:border-primary px-6 py-4 text-sm dark:text-white dark:placeholder:text-white/40 outline-none"/>
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-xl transition-all shadow-lg shadow-primary/20">Subscribe</button>
            </form>
        </div>
    </section>
</div>
</main>

<?php require 'footer.php'; ?>
