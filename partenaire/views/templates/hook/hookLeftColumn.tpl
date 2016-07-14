{if isset($data) && $data }
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function () {

            $("#partenaire_leftCol").owlCarousel({
                items: 1,
                loop: true,
                responsive: true,
                autoPlay: 3000,
                /*navigationText : ["<<",">>"],*/
                navigation: false,
                afterInit: function (elem) {
                    var that = this
                    that.owlControls.prependTo(elem)
                }
            });

        });
    </script>
    {*OWL CAROUSEL START HERE*}
    <div class="container leftcol-partenaire">
        <div class="row">
            <div class="col-md-12 block">
                <p class="tab_title title_block">
                    Ils nous font confiance
                </p>
            </div>
        </div>
        <div id="partenaire_leftCol">
            {foreach $data as $items}
                <div class="row">
                    <div class="col-md-12">
                        <img src="http://www.medica-services.fr/upload/{$items.image}" class="left_col_partenaire_img"
                             alt="{$items.nom}">
                        <h3 class="partenaire_name">{$items.nom}</h3>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    {*OWL CAROUSEL END HERE*}
{/if}