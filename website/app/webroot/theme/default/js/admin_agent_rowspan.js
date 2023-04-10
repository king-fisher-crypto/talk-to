var nx_admin_agent_rowspan = {
    init: function(){
        //On récupère le nombre de ligne du tableau
        var tableRows = $('span[rows]').attr('rows');
        //On modifie l'attribut rowspan de la cellule des boutons
        $('td[rowspan]').attr('rowspan', tableRows);

        //On supprime la span qui contenait le nombre de ligne du tableau
        $('span[rows]').remove();
    }
}

$(document).ready(function(){ nx_admin_agent_rowspan.init(); });