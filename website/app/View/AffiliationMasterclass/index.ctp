<script src="https://cdn.jsdelivr.net/npm/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<?php
$videos = [
    [
        'src' => 'https://www.youtube.com/embed/UYwF-jdcVjY',
        'rating' => 2.5,
        'allPrice' => 499,
        'price' => 299,
        'dates' => '15/03/21, 25/05/22, 15/08/22, 22/05/22, 12/02/22, 21/02/22, 15/07/22, 15/05/22',
        'name' => 'Sebastien Nagy',
        'title' => 'MasterClass Titre',
        'subtitle' => 'Sous-titre masterclass',
        'description' => 'Masterclass présentation, consectetur adipiscing elit. Etiam aliquam fringilla lacus at ut tempus lacus ac. Convallis elit ac vestibulum arcu id.
        <span class="more">consectetur adipiscing elit. Aliquet commodo in nunc amet. Adipiscing pharetra varius arcu ultricies gravida id mauris, nulla at. Morbi tincidunt nisi, volutpat arcu, mi. Nunc ac in aliquet leo vitae diam. A etiam accumsan sed velit elementum. Etiam quisque hendrerit sagittis ut. In in ut a tempus adipiscing urna in nec eu. Vitae, suspendisse sed velit nisl non. Sed sit facilisi euismod elementum. Id massa est tortor dui enim cursus pharetra donec sem. Adipiscing at et, bibendum mauris viverra vel adipiscing quis orci. Pellentesque hendrerit eu sed odio et dignissim aliquet sed.
            Porttitor adipiscing posuere nisl quisque ac, accumsan lectus et, non. Gravida nisi vel fermentum ultricies cras non amet cursus vestibulum. Id facilisis laoreet consectetur quam. Phasellus sed ipsum lobortis mauris tellus dolor, cras nascetur consequat. Quis cursus diam proin ac. Purus leo urna, porttitor enim egestas viverra lacinia eget. Neque, elit scelerisque mattis mi lectus. Massa tortor ac amet, rhoncus malesuada accumsan sagittis odio nunc. Habitasse leo sed pellentesque rhoncus eleifend in. Vitae sed tincidunt adipiscing vitae leo. A ipsum phasellus vivamus diam.',
    ],
    [
        'src' => null,
        'rating' => 2.5,
        'allPrice' => 499,
        'price' => 299,
        'dates' => '15/03/21, 25/05/22, 15/08/22, 22/05/22, 12/02/22, 21/02/22, 15/07/22, 15/05/22',
        'name' => 'Sebastien Nagy',
        'title' => 'MasterClass Titre',
        'subtitle' => 'Sous-titre masterclass',
        'description' => 'Masterclass présentation, consectetur adipiscing elit. Etiam aliquam fringilla lacus at ut tempus lacus ac. Convallis elit ac vestibulum arcu id.
        <span class="more">consectetur adipiscing elit. Aliquet commodo in nunc amet. Adipiscing pharetra varius arcu ultricies gravida id mauris, nulla at. Morbi tincidunt nisi, volutpat arcu, mi. Nunc ac in aliquet leo vitae diam. A etiam accumsan sed velit elementum. Etiam quisque hendrerit sagittis ut. In in ut a tempus adipiscing urna in nec eu. Vitae, suspendisse sed velit nisl non. Sed sit facilisi euismod elementum. Id massa est tortor dui enim cursus pharetra donec sem. Adipiscing at et, bibendum mauris viverra vel adipiscing quis orci. Pellentesque hendrerit eu sed odio et dignissim aliquet sed.
            Porttitor adipiscing posuere nisl quisque ac, accumsan lectus et, non. Gravida nisi vel fermentum ultricies cras non amet cursus vestibulum. Id facilisis laoreet consectetur quam. Phasellus sed ipsum lobortis mauris tellus dolor, cras nascetur consequat. Quis cursus diam proin ac. Purus leo urna, porttitor enim egestas viverra lacinia eget. Neque, elit scelerisque mattis mi lectus. Massa tortor ac amet, rhoncus malesuada accumsan sagittis odio nunc. Habitasse leo sed pellentesque rhoncus eleifend in. Vitae sed tincidunt adipiscing vitae leo. A ipsum phasellus vivamus diam.',


    ],
    [
        'src' => null,
        'rating' => 2.5,
        'allPrice' => 499,
        'price' => 299,
        'dates' => '15/03/21, 25/05/22, 15/08/22, 22/05/22, 12/02/22, 21/02/22, 15/07/22, 15/05/22',
        'name' => 'Sebastien Nagy',
        'title' => 'MasterClass Titre',
        'subtitle' => 'Sous-titre masterclass',
        'description' => 'Masterclass présentation, consectetur adipiscing elit. Etiam aliquam fringilla lacus at ut tempus lacus ac. Convallis elit ac vestibulum arcu id.
        <span class="more">consectetur adipiscing elit. Aliquet commodo in nunc amet. Adipiscing pharetra varius arcu ultricies gravida id mauris, nulla at. Morbi tincidunt nisi, volutpat arcu, mi. Nunc ac in aliquet leo vitae diam. A etiam accumsan sed velit elementum. Etiam quisque hendrerit sagittis ut. In in ut a tempus adipiscing urna in nec eu. Vitae, suspendisse sed velit nisl non. Sed sit facilisi euismod elementum. Id massa est tortor dui enim cursus pharetra donec sem. Adipiscing at et, bibendum mauris viverra vel adipiscing quis orci. Pellentesque hendrerit eu sed odio et dignissim aliquet sed.
            Porttitor adipiscing posuere nisl quisque ac, accumsan lectus et, non. Gravida nisi vel fermentum ultricies cras non amet cursus vestibulum. Id facilisis laoreet consectetur quam. Phasellus sed ipsum lobortis mauris tellus dolor, cras nascetur consequat. Quis cursus diam proin ac. Purus leo urna, porttitor enim egestas viverra lacinia eget. Neque, elit scelerisque mattis mi lectus. Massa tortor ac amet, rhoncus malesuada accumsan sagittis odio nunc. Habitasse leo sed pellentesque rhoncus eleifend in. Vitae sed tincidunt adipiscing vitae leo. A ipsum phasellus vivamus diam.',


    ],
    [
        'src' => null,
        'rating' => 2.5,
        'allPrice' => 499,
        'price' => 299,
        'dates' => '15/03/21, 25/05/22, 15/08/22, 22/05/22, 12/02/22, 21/02/22, 15/07/22, 15/05/22',
        'name' => 'Sebastien Nagy',
        'title' => 'MasterClass Titre',
        'subtitle' => 'Sous-titre masterclass',
        'description' => 'Masterclass présentation, consectetur adipiscing elit. Etiam aliquam fringilla lacus at ut tempus lacus ac. Convallis elit ac vestibulum arcu id.
        <span class="more">consectetur adipiscing elit. Aliquet commodo in nunc amet. Adipiscing pharetra varius arcu ultricies gravida id mauris, nulla at. Morbi tincidunt nisi, volutpat arcu, mi. Nunc ac in aliquet leo vitae diam. A etiam accumsan sed velit elementum. Etiam quisque hendrerit sagittis ut. In in ut a tempus adipiscing urna in nec eu. Vitae, suspendisse sed velit nisl non. Sed sit facilisi euismod elementum. Id massa est tortor dui enim cursus pharetra donec sem. Adipiscing at et, bibendum mauris viverra vel adipiscing quis orci. Pellentesque hendrerit eu sed odio et dignissim aliquet sed.
            Porttitor adipiscing posuere nisl quisque ac, accumsan lectus et, non. Gravida nisi vel fermentum ultricies cras non amet cursus vestibulum. Id facilisis laoreet consectetur quam. Phasellus sed ipsum lobortis mauris tellus dolor, cras nascetur consequat. Quis cursus diam proin ac. Purus leo urna, porttitor enim egestas viverra lacinia eget. Neque, elit scelerisque mattis mi lectus. Massa tortor ac amet, rhoncus malesuada accumsan sagittis odio nunc. Habitasse leo sed pellentesque rhoncus eleifend in. Vitae sed tincidunt adipiscing vitae leo. A ipsum phasellus vivamus diam.',
    ],
]
?>
<style>
    @import url(//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css);
</style>
<section class="affilation-masterclass-page">
    <div class="header-img-container">
        <div class="text-container">
            <h1>Affilitation MasterClass Livitalk </h1>
            <p>Faites la promotion des Masterclass de vos <span style="color: var(--blue);">LiviMasters</span> préférés auprès de votre entourage, vos clients ou vos followers et gagnez de l'argent lors de chaque Masterclass vendue grâce à votre lien d'affiliation</p>
        </div>
    </div>
    <div class="videos-bloc">
        <h1>Affiliation Masterclass</h1>
        <div class="settings">
            <div class="search_language">
                <div class="search_container">
                    <div class="searchInput">
                        <img src="\theme\black_blue\img\promo_code_agent\icon_chercher_chercher_default.svg" alt="">
                        <input type="text" placeholder="<?= __("tapez mot clé") ?>">
                    </div>
                </div>
                <div class="lang">
                    <span><?= __("Langue Masterclass") ?>:</span>
                    <div class="selectoption">
                        <!-- <select name="lang" id="videolang" class="select">
                            <option value="Fr">Français</option>
                            <option value="En">English</option>
                        </select> -->
                        <select>
                            <option value="1" selected="selected">Français</option>
                            <option value="2">Anglais</option>
                            <option value="3">Allemand</option>
                            <option value="4">Espagnol</option>
                            <option value="5">Italien</option>
                            <option value="7">Portugais</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="tri">
                <span><?= __("Trier par ") ?>:</span>
                <div class="tri_options first">
                    <button>Gains le plus élevé</button>
                    <button>Gains le plus bas</button>
                </div>
                <div class="tri_options">
                    <button>plus récent</button>
                    <button>Les + Populaires</button>
                </div>
            </div>
        </div>
        <div class="video-wrapper">
            <?php foreach ($videos as $video) : ?>
                <div class="card-video">
                    <div class="videos-links column">
                        <div class="videos-links">
                            <div class="video-container">

                                <div class="img">
                                    <?php if (isset($video['src'])) : ?>
                                        <div class="img-bloc-youtube">
                                            <iframe width="420" height="345" src="<?= $video['src'] ?>" class="video-img">
                                            </iframe>
                                        </div>
                                    <?php else : ?>
                                        <div class="img-bloc-youtube default">
                                            <img src="\theme\black_blue\img\affiliation-masterclass\video_img.svg" alt="" class="video-img no-video">
                                        </div>
                                    <?php endif; ?>

                                    <?php echo $this->element('Utils/rating', ['rating' => $video['rating']]); ?>

                                    <div class="mobile">
                                        <p class="all-price"><span class="cross"><?= $video['allPrice'] ?>$</span> <span class="price"><?= $video['price'] ?>$</span></p>
                                        <p class="date">Dates: <span class="blue"><?= $video['dates'] ?></span></p>

                                    </div>
                                </div>

                            </div>
                            <div class="content-video">
                                <h1><?= $video['title'] ?> <span>-</span><?= $video['name'] ?></h1>
                                <h3><?= $video['subtitle'] ?></h3>
                                <p class="date">Dates: <span class="blue"><?= $video['dates'] ?></span></p>
                                <p class="all-price"><span class="cross"><?= $video['allPrice'] ?>$</span> <span class="price"><?= $video['price'] ?>$</span></p>
                                <div class="desktop-main-content">
                                    <p class="content-desc"><?= $video['description'] ?>
                                    </p>
                                    <div class="arrow-back">
                                        <img class="arrow_top" src="/theme/black_blue/img/arrow_right.svg">
                                    </div>

                                    <div class="photo-container">
                                        <p>Promouvoir Photographie aérienne et gagnez <span>100$</span> par vente générée, en diffusant votre lien d'affiliation.</p>
                                    </div>
                                    <div class="affiliation-link">
                                        <button>Obtenir mon lien d'affiliation</button>
                                    </div>
                                    <div class="copy-link">
                                        <div class="link-img">
                                            <img src="\theme\black_blue\img\affiliation-masterclass\link.svg">
                                        </div>
                                        <input type="text" value="https://livitalk.com/affiliation">
                                        <div class="copy-btn">
                                            <button class="btn orange">Copier</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tablette-main-content">
                            <p class="content-desc"><?= $video['description'] ?>
                            </p>
                            <div class="arrow-back">
                                <img class="arrow_top" src="/theme/black_blue/img/arrow_right.svg">
                            </div>

                            <div class="photo-container">
                                <p>Promouvoir Photographie aérienne et gagnez <span>100$</span> par vente générée, en diffusant votre lien d'affiliation.</p>
                            </div>
                            <div class="affiliation-link">
                                <button>Obtenir mon lien d'affiliation</button>
                            </div>
                            <div class="copy-link">
                                <div class="link-img">
                                    <img src="\theme\black_blue\img\affiliation-masterclass\link.svg">
                                </div>
                                <input type="text" value="https://livitalk.com/affiliation">
                                <div class="copy-btn">
                                    <button class="btn orange">Copier</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

        <div class="arrow-scroll" id="backToTop">
            <img src="\theme\black_blue\img\affiliation-masterclass\arr.svg">
        </div>
</section>
<script>
    let descText = document.querySelectorAll(".content-desc");

    descText.forEach(el => {
        let text = el.textContent;
        if (text.length > 144) {
            let part_one = text.slice(0, 145);
            let part_two = text.slice(145, el.length);
            el.textContent = part_one;
            let span1 = document.createElement("span");
            span1.innerHTML = `Lire la suite<img class="read-more-arrow" src="/theme/black_blue/img/arrow_right.svg">`;
            span1.classList.add("dots");
            el.appendChild(span1)
            let span2 = document.createElement("span");
            span2.textContent = part_two;
            span2.classList.add("more");
            el.appendChild(span2)

        }

    })



    let link_giver = document.querySelectorAll(".affiliation-link button")
    let dots = document.querySelectorAll(".dots");
    let moreText = document.getElementById("more");
    let triBtn = document.querySelectorAll('.tri_options button')
    triBtn.forEach(el => el.addEventListener('click', (e) => {
        e.currentTarget.classList.toggle('blue')
    }))
    let back = document.querySelectorAll(".arrow-back .arrow_top")
    dots.forEach(el => el.addEventListener('click', (e) => {
        e.currentTarget.parentElement.childNodes[2].style.display = 'block'
        e.currentTarget.style.display = 'none'
        e.currentTarget.parentElement.nextSibling.style.display = 'block'
    }))

    back.forEach(el => el.addEventListener('click', (e) => {
        e.currentTarget.parentElement.previousSibling.childNodes[1].style.display = 'flex'
        e.currentTarget.parentElement.previousSibling.childNodes[2].style.display = 'none'
        e.currentTarget.parentElement.style.display = 'none'
    }))

    link_giver.forEach(el => el.addEventListener('click', (e) => {
        e.target.parentElement.nextSibling.style.display = 'block'
        e.target.parentElement.nextSibling.style.opacity = 1
        e.target.parentElement.style.display = 'none'
    }))

    const copyBtns = document.querySelectorAll('.copy-btn button');

    copyBtns.forEach(copyBtn => {
        const input = copyBtn.closest('.copy-link').querySelector('input');

        copyBtn.addEventListener('click', () => {
            // Copy input value to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(input.value).then(() => {
                    // Change button text to "Copié"
                    copyBtn.textContent = 'Copié';
                    copyBtn.classList.remove('orange')
                    copyBtn.classList.add('blue')
                }, (err) => {
                    console.error('Failed to copy: ', err);
                });
            } else {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = input.value;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);

                // Change button text to "Copié"
                copyBtn.textContent = 'Copié';
                copyBtn.classList.remove('orange')
                copyBtn.classList.add('blue')
            }
        });
    });

    const scroll = new SmoothScroll();

    // Add a click event listener to the chevron icon
    document.getElementById("backToTop").addEventListener("click", function() {
        // Scroll to the element using Smooth Scroll
        scroll.animateScroll(document.body, null, {
            duration: 2000
        });
    });
</script>