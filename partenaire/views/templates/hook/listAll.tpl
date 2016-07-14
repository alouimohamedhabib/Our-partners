{if isset($data) }
    <div class="row">
        <div class="col-md-2 col-md-push-10">
            <a id="parteners-new" class="list-toolbar-btn"
               href="#">
							<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Ajouter"
                                  data-html="true" data-placement="left">
								<i class="process-icon-new"></i>
							</span>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table">

                <tr>
                    <td>Partenaire</td>
                    <td>Action</td>
                </tr>
                {foreach $data as $item}
                    <tr>
                        <td class="col-md-10">{$item.nom}</td>
                        <td>
                            <a href="{$link}&idPartenaire={$item.id_partenaire}">Modifier</a>
                            <a href="{$link}&idPartenaire={$item.id_partenaire}">Supprimer</a>
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    </div>
{/if}
{*http://www.medica-services.fr/d/administration/www.medica-services.fr/d/administration/index.php?controller=AdminModules&configure=partenaire&token=44f197692c7c8b5eb0922edd89e22694&do=list16*}