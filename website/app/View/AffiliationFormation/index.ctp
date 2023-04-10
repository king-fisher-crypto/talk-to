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
        'title' => 'Photographie aérienne',
        'subtitle' => 'Sous-titre formation',
        'description' => 'Formation à la photographie en 20 heures de vidéo, apprendre à cadrer, faire des effets de style, conseils de photographe professionnel, accès à vie. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Urna nisi eu urna tempus arcu elementum. Egestas est tellus enim facilisi mi, senectus. Ipsum tincidunt pellentesque ornare auctor tincidunt tellus integer eget. Odio elit at mattis vel risus interdum senectus venenatis laoreet.
        Purus non nibh sollicitudin ipsum porttitor. Metus, nulla adipiscing faucibus dolor, dui quam mollis. Massa, vitae eget lorem ut amet mauris augue mi. Lacus eu, commodo consequat amet hac nam et. Purus elementum sollicitudin arcu sed aliquam eu, vestibulum, morbi. Odio vestibulum ultricies dui, vitae viverra tellus. Scelerisque sollicitudin condimentum enim, id est suspendisse elit. Eget id vel luctus consequat et. Cras ut magna pulvinar vel eros, tristique gravida maecenas sollicitudin. Ac ac diam duis consectetur nibh sed congue lacus. Nulla cras cursus diam diam sollicitudin. Eget tortor faucibus tellus amet nisl, tellus, iaculis massa. Leo arcu faucibus volutpat in bibendum fusce sit velit nec. Lacus risus dictumst nibh nec malesuada.
        Nascetur ac feugiat eu, turpis felis commodo felis elementum. Faucibus netus nunc pulvinar pharetra, blandit. Rutrum mauris sed nibh sit donec eget.',
        'textBtn' => "Promouvoir Photographie aérienne et gagnez <span>100$</span> par vente générée, en diffusant votre lien d'affiliation."
    ],
    [
        'src' => 'https://www.youtube.com/embed/UYwF-jdcVjY',
        'rating' => 3,
        'allPrice' => 499,
        'price' => 299,
        'dates' => '15/03/21, 25/05/22, 15/08/22, 22/05/22, 12/02/22, 21/02/22, 15/07/22, 15/05/22',
        'name' => 'Floriant Boullot',
        'title' => 'SOPHRO ASMR',
        'subtitle' => 'Sous-titre formation',
        'description' => 'Utilisez votre stress pour vous sentir plus calme, plus heureux au quotidien et même mieux dormir grâce au sophro-asmr de florian ballot. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Urna nisi eu urna tempus arcu elementum. Egestas est tellus enim facilisi mi, senectus. Ipsum tincidunt pellentesque ornare auctor tincidunt tellus integer eget. Odio elit at mattis vel risus interdum senectus venenatis laoreet.
        Purus non nibh sollicitudin ipsum porttitor. Metus, nulla adipiscing faucibus dolor, dui quam mollis. Massa, vitae eget lorem ut amet mauris augue mi. Lacus eu, commodo consequat amet hac nam et. Purus elementum sollicitudin arcu sed aliquam eu, vestibulum, morbi. Odio vestibulum ultricies dui, vitae viverra tellus. Scelerisque sollicitudin condimentum enim, id est suspendisse elit. Eget id vel luctus consequat et. Cras ut magna pulvinar vel eros, tristique gravida maecenas sollicitudin. Ac ac diam duis consectetur nibh sed congue lacus. Nulla cras cursus diam diam sollicitudin. Eget tortor faucibus tellus amet nisl, tellus, iaculis massa. Leo arcu faucibus volutpat in bibendum fusce sit velit nec. Lacus risus dictumst nibh nec malesuada.
        Nascetur ac feugiat eu, turpis felis commodo felis elementum. Faucibus netus nunc pulvinar pharetra, blandit. Rutrum mauris sed nibh sit donec eget.',
        'textBtn' => "Promouvoir SOPHRO ASMR et gagnez <span>89,90€</span> par vente générée, en diffusant votre lien d'affiliation."


    ],
    [
        'src' => 'https://www.youtube.com/embed/UYwF-jdcVjY',
        'rating' => 3,
        'allPrice' => 499,
        'price' => 299,
        'dates' => '15/03/21, 25/05/22, 15/08/22, 22/05/22, 12/02/22, 21/02/22, 15/07/22, 15/05/22',
        'name' => 'David Okalmy',
        'title' => 'Maître Photographie',
        'subtitle' => 'Sous-titre formation',
        'description' => 'Formation à la photographie en 20 heures de vidéo, apprendre à cadrer, faire des effets de style, conseils de photographe professionnel, accès à vie.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Urna nisi eu urna tempus arcu elementum. Egestas est tellus enim facilisi mi, senectus. Ipsum tincidunt pellentesque ornare auctor tincidunt tellus integer eget. Odio elit at mattis vel risus interdum senectus venenatis laoreet.
        Purus non nibh sollicitudin ipsum porttitor. Metus, nulla adipiscing faucibus dolor, dui quam mollis. Massa, vitae eget lorem ut amet mauris augue mi. Lacus eu, commodo consequat amet hac nam et. Purus elementum sollicitudin arcu sed aliquam eu, vestibulum, morbi. Odio vestibulum ultricies dui, vitae viverra tellus. Scelerisque sollicitudin condimentum enim, id est suspendisse elit. Eget id vel luctus consequat et. Cras ut magna pulvinar vel eros, tristique gravida maecenas sollicitudin. Ac ac diam duis consectetur nibh sed congue lacus. Nulla cras cursus diam diam sollicitudin. Eget tortor faucibus tellus amet nisl, tellus, iaculis massa. Leo arcu faucibus volutpat in bibendum fusce sit velit nec. Lacus risus dictumst nibh nec malesuada.
        Nascetur ac feugiat eu, turpis felis commodo felis elementum. Faucibus netus nunc pulvinar pharetra, blandit. Rutrum mauris sed nibh sit donec eget.',
        'textBtn' => "Promouvoir maître photographie et gagnez <span>89,90€</span> par vente générée, en diffusant votre lien d'affiliation."


    ],
    [
        'src' => 'https://www.youtube.com/embed/UYwF-jdcVjY',
        'rating' => 3,
        'allPrice' => 499,
        'price' => 299,
        'dates' => '15/03/21, 25/05/22, 15/08/22, 22/05/22, 12/02/22, 21/02/22, 15/07/22, 15/05/22',
        'name' => 'David Okalmy',
        'title' => 'Maître Model',
        'subtitle' => 'Sous-titre formation',
        'description' => 'Formation à la photographie en 20 heures de vidéo, apprendre à cadrer, faire des effets de style, conseils de photographe professionnel, accès à vie.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Urna nisi eu urna tempus arcu elementum. Egestas est tellus enim facilisi mi, senectus. Ipsum tincidunt pellentesque ornare auctor tincidunt tellus integer eget. Odio elit at mattis vel risus interdum senectus venenatis laoreet.
        Purus non nibh sollicitudin ipsum porttitor. Metus, nulla adipiscing faucibus dolor, dui quam mollis. Massa, vitae eget lorem ut amet mauris augue mi. Lacus eu, commodo consequat amet hac nam et. Purus elementum sollicitudin arcu sed aliquam eu, vestibulum, morbi. Odio vestibulum ultricies dui, vitae viverra tellus. Scelerisque sollicitudin condimentum enim, id est suspendisse elit. Eget id vel luctus consequat et. Cras ut magna pulvinar vel eros, tristique gravida maecenas sollicitudin. Ac ac diam duis consectetur nibh sed congue lacus. Nulla cras cursus diam diam sollicitudin. Eget tortor faucibus tellus amet nisl, tellus, iaculis massa. Leo arcu faucibus volutpat in bibendum fusce sit velit nec. Lacus risus dictumst nibh nec malesuada.
        Nascetur ac feugiat eu, turpis felis commodo felis elementum. Faucibus netus nunc pulvinar pharetra, blandit. Rutrum mauris sed nibh sit donec eget.',
        'textBtn' => "Promouvoir maître model et gagnez <span>89,90€</span> par vente générée, en diffusant votre lien d'affiliation."

    ],
]
?>
<style>
    @import url(//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css);
</style>
<section class="affilation-formation-page">
    <div class="header-img-container">
        <div class="text-container">
            <h1>Affilitation Vidéos Formations Livitalk </h1>
            <p>Faites la promotion des formation de vos <span style="color: var(--blue);">LiviMasters</span> préférés auprès de votre entourage, vos clients ou vos followers et gagnez de l'argent lors de chaque formation vendue grâce à votre lien d'affiliation</p>
        </div>
    </div>
    <div class="videos-bloc">
        <h1>Affiliation Formation  Vidéos </h1>
        <div class="settings">
            <div class="search_language">
                <div class="search_container">
                    <div class="searchInput">
                        <img src="\theme\black_blue\img\promo_code_agent\icon_chercher_chercher_default.svg" alt="">
                        <input type="text" placeholder="<?= __("tapez mot clé") ?>">
                    </div>
                </div>
                <div class="lang">
                    <span><?= __("Langue Vidéo") ?>:</span>
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
                                            <img src="\theme\black_blue\img\affiliation-formation\video_img.svg" alt="" class="video-img no-video">
                                        </div>
                                    <?php endif; ?>

                                    <?php echo $this->element('Utils/rating', ['rating' => $video['rating']]); ?>

                                    <div class="mobile">
                                        <p class="all-price"> <span class="cross"><?= $video['allPrice'] ?>$</span><span class="price"><?= $video['price'] ?>$</span></p>
                                        <p class="date">Dernière mise à jour : 02 juillet 2021 par: <span class="blue"><?= $video['name'] ?></span></p>

                                    </div>
                                </div>

                            </div>
                            <div class="content-video">
                                <h1 class="desktop"><?= $video['name'] ?> <span>-</span> <?= $video['title'] ?></h1>
                                <h1 class="mobile"><?= $video['name'] ?></h1>
                                <h1 class="mobile second-child"><?= $video['title'] ?></h1>
                                <h3><?= $video['subtitle'] ?></h3>
                                <p class="date">Dernière mise à jour : 02 juillet 2021 par : <span class="blue"><?= $video['name'] ?></span></p>
                                <p class="all-price"><span class="cross"><?= $video['allPrice'] ?>$ </span><span class="price"><?= $video['price'] ?>$</span></p>
                                <div class="desktop-main-content">
                                    <p class="content-desc"><?= $video['description'] ?>
                                    </p>
                                    <div class="arrow-back">
                                        <img class="arrow_top" src="/theme/black_blue/img/arrow_right.svg">
                                    </div>

                                    <div class="photo-container">

                                        <p><?= $video['textBtn'] ?></p>
                                    </div>
                                    <div class="affiliation-link desktop">
                                        <button>Obtenir mon lien d'affiliation</button>
                                    </div>
                                    <div class="affiliation-link no-desktop">
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
                                <p><?= $video['textBtn'] ?></p>
                            </div>
                            <div class="affiliation-link  no-desktop">
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
                    <div class="desktop-popup">
                        <div class="container">
                            <h1>Lien Affiliation Vidéos Formation</h1>
                            <div class="copy-link">
                                <div class="link-img">
                                    <img src="\theme\black_blue\img\affiliation-masterclass\link.svg">
                                </div>
                                <input type="text" value="https://livitalk.com/affiliation">
                                <div class="copy-btn"> <button class="btn orange">Copier</button></div>
                            </div>

                            <p>Faites connaitre les Vidéos formations de vos LiviMasters préférés sur vos réseaux sociaux, vos clients et votre entourage et gagnez de l'argent lors que chaque vente grâce à votre lien d'affiliation.</p>
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
    letallDate = document.querySelectorAll('.date')
    for (let i = 2; i < letallDate.length; i++) {
        letallDate[i].classList.add("second-child")
    }

    descText.forEach(el => {
        let text = el.textContent;
        if (text.length > 144) {
            let part_one = text.slice(0, 148);
            let part_two = text.slice(148, el.length);
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



    let desktop_link_giver = document.querySelectorAll(".affiliation-link.desktop button")
    let link_giver = document.querySelectorAll(".affiliation-link.no-desktop button")
    let dots = document.querySelectorAll(".dots");
    let moreText = document.getElementById("more");
    let triBtn = document.querySelectorAll('.tri_options button')
    for(let i = 0;i<triBtn.length;i++){
        triBtn[i].addEventListener('click',()=>{
            triBtn.forEach(el=>{
                el.classList.remove('blue')
            })
            console.log('1')
            triBtn[i].classList.add('blue')
        })
    }
    let back = document.querySelectorAll(".arrow-back .arrow_top")
    dots.forEach(el => el.addEventListener('click', (e) => {
        e.currentTarget.parentElement.childNodes[2].style.display = 'inline'
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

    desktop_link_giver.forEach(el => {
        el.addEventListener('click', (e) => {
            e.currentTarget.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.display = 'flex';
            e.currentTarget.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.position = 'relative';
            e.currentTarget.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.childNodes[1].style.right = '-24%'
            e.currentTarget.parentElement.parentElement.parentElement.parentElement.parentElement.style.transform = "translateX(-13%)";
            e.currentTarget.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.childNodes[1].style.opacity = 1;
            e.currentTarget.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.childNodes[1].style.display = 'block'
        })
    })







    $(window).resize(function() {
        let w = getDocWidth();

        if (w <= 1024) {

            desktop_link_giver.forEach(el => {
                el.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.display = 'block';
                el.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.position = 'inherit';
                el.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.childNodes[1].style.right = 'inherit'
                el.parentElement.parentElement.parentElement.parentElement.parentElement.style.transform = "none";
                el.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.childNodes[1].style.opacity = 0;
                el.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.childNodes[1].style.display = 'none'
            })
        }
    })

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