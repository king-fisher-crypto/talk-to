
<h2 class="p35 t28 m20 fw500 top"><?= __("Photo") ?></h2>

<div class="foto_couv" style="background: url(/assets/img/bg/2.jpg) no-repeat center center; background-size: cover; ">


    <div class="vignette_box"> <img src="https://picsum.photos/200/300" class="rounded"> <img src="/theme/black_blue/img/modifier_bleu_disk.svg" class="picto_modif"> </div>


    <img src="/theme/black_blue/img/modifier_bleu_disk.svg" class="picto_modif couv">
</div>

<a class=" btn_pres changer_foto p25 t18 m18 up_case"  title="<?= __("valider la photo") ?>"><?= __("valider la photo") ?></a>


<h2 class="p35 t28 m20 fw500 "><?= __("Ma Présentation") ?></h2>

<div class="ss_titre"><?= __("Cette présentation sera visible sur le site.") ?></div>

<div class="txt_presentation p24 t18 m16 fw400 ">

    <textarea id="presentation" placeholder="<?= __("Tapez votre texte ici") ?>" style="width: 100%; max-width: 100%;" >Lorem ipsum dolor sit amet, consectetur adipiscing elit. Purus arcu potenti nec non eget netus ornare. Nam in ac in dolor venenatis. Volutpat id in sit tortor non. Mollis aliquam amet blandit volutpat ut sed blandit sed. Urna amet, ultrices scelerisque egestas tortor faucibus in tincidunt urna. Lectus sit aliquet sed imperdiet eget ac orci turpis. Convallis ornare imperdiet erat neque morbi nibh. Congue consequat magna ac, sit mauris. Massa cursus vehicula diam pellentesque. Ut tellus sit lacus, dolor semper sollicitudin arcu. Semper sit elit quisque sagittis. Aliquam, commodo velit hendrerit lorem sed porttitor habitasse ullamcorper. Id sem tempus at nam consectetur egestas ac. Senectus suspendisse in blandit vel arcu sed massa, semper tempor.
Non dignissim sed pretium ac urna. Blandit quam feugiat sodales dui netus varius. Mollis eleifend cursus sapien vitae. Aliquam turpis in mauris, commodo tortor est. Eget amet lectus venenatis orci eu libero scelerisque tristique cras. Tempor mi enim sed in purus cras dolor felis id. Pellentesque lectus ut netus aliquet viverra enim, eget ac mauris. Aliquet senectus leo diam facilisi consequat tortor. Aliquam lobortis vehicula lobortis volutpat, justo, vestibulum arcu. Consequat non accumsan, tellus in dictum. Consequat in quam urna nulla. Risus lobortis id tortor sed duis. Turpis ut augue ut eget diam aliquam elit semper.</textarea>

</div>






<a class=" btn_pres  p25 t18 m18 up_case"  title="<?= __("modifier") ?>"><?= __("modifier") ?></a>

<h2 class="p35 t28 m20 fw500"><?= __("Information clients pour une consultation par email") ?><span class="p24 ast orange2 fw500">*</span></h2>

<div class="ss_titre"><?= __("Indiquez dans le champs les éléments dont vous avez besoin pour une consultation par Email, ces indications apparaîtront sur le site avant envoi du client.") ?></div>

<div class="borshad div_delai_mail">

    <div class="p24 t16 p16 fw400 txt lgrey2" contenteditable="true">

	<?= __("Tapez votre texte") ?>
    </div>

    <div id="tabs_k10" class="delai-de-reponse-par-email-container _form_input" style="display: block;">
        <div class="delai-de-reponse-par-email-section">
            <p class="cs-title lh24-36 fw400 cs-margin-0"><span>Délai de réponse par Email</span></p>
            <div class="cs-setting cs-radio cs-list-hours">
                <label class="cs-control">
                    <input type="radio" name="cs-hour" value="6h">
                    <p class="lh24-36 fw400 cs-margin-0"><span>6h</span></p>
                </label>
                <label class="cs-control">
                    <input type="radio" name="cs-hour" value="12h">
                    <p class="lh24-36 fw400 cs-margin-0"><span>12h</span></p>
                </label>
                <label class="cs-control">
                    <input type="radio" name="cs-hour" value="24h">
                    <p class="lh24-36 fw400 cs-margin-0"><span>24h</span></p>
                </label>
                <label class="cs-control">
                    <input type="radio" name="cs-hour" value="48h">
                    <p class="lh24-36 fw400 cs-margin-0"><span>48h</span></p>
                </label>
            </div>
        </div>
    </div>


</div>

<a class=" btn_pres  p25 t18 m18 up_case"  title="<?= __("modifier") ?>"><?= __("modifier") ?></a>


<h2 class="p35 t28 m20 fw500"><?= __("Ma Présentation Audio") ?></h2>
<div class="path_btn">
    <div class="path p25 t16">Disque local (D:) > audio > présentation </div>

    <a class=" btn_pres telecharger p25 t18 m18 up_case"  title="<?= __("télécharger ") ?>"><?= __("télécharger") ?></a>


</div>



<script>
    document.addEventListener("DOMContentLoaded", function ()
    {


        function call_fit_content() {  height_fit_content("presentation");  }

        $("#presentation").on("keyup change", function (e)
        {
            call_fit_content()
        })

	setTimeout(call_fit_content, 3000)
        call_fit_content()
	

    });
</script>