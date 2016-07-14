{if isset($data) && $data }
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function () {

            $("#partenaire").owlCarousel({
                items: 1,
                navigation: true,
                navigationText : ["<<",">>"],
                afterInit: function (elem) {
                    var that = this
                    that.owlControls.prependTo(elem)
                }
            });

        });
    </script>
    {*OWL CAROUSEL START HERE*}
    <div class="container">
        <div id="partenaire">
            {foreach $data as $items}
                <div class="row">
                    <div class="col-md-6">
                        <img src="http://www.medica-services.fr/upload/{$items.image}" class="partenaire_img"
                             alt="{$items.nom}">
                    </div>
                    <div class="col-md-6">
                        <h2 class="partenaire_name">{$items.nom}</h2>
                        {if $desc == 'true'}
                            <div class="partenaire_description">{$items.descriptif}</div>
                        {/if}
                        <a class="partenaire_link" target="_blank" href="{$items.lien}">Site</a>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    {*OWL CAROUSEL END HERE*}
{/if}