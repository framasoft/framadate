{extends file='page.tpl'}

{block name=main}
    <div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Problème d'accès :</span>
        C'est embarassant... Il semblerait pour que vous n'ayez pas le droit d'accéder à cette partie... Pour plus d'informations, veuillez contacter votre administrateur.
    </div>

    <a href="/"><span class="glyphicon glyphicon-arrow-left aria-hidden="true"></span> Retour à l'accueil</a>

{/block}
