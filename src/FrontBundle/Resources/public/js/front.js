$(document).ready(function(){
    $('#panier-list').DataTable({
        "processing": true,
        "serverSide": false,
        "searching": true,
        "paging": true,
        "info": false,
        "data": getCommand(),
        order: [],
        "pageLength": 10,
        "bLengthChange": false,
        "autoWidth": false,
        "columns": [
            {"data": "titre"},
            {"data": "description"},
            {"data": "prix"},
            {"data": "quantite"},
            {
                "targets": 0,
                "searchable": false,
                "orderable": false,
                "render": function(data, type, row){
                    return '<a href="#" class="voirFiche" data-id="'+row.id+'">Voir fiche</a>';
                }
            }
        ]
    });
});

function getCommand() {
    var produitCommand = localStorage.getItem('command') !== null ? localStorage.getItem('command') : "{}";
    produitCommand = JSON.parse(produitCommand);
    var produits = [];
    $.each(produitCommand, function (index, value) {
        produits.push({
            "id": value.id,
            "titre": value.titre,
            "description": value.description,
            "prix": value.prix,
            "quantite": value.quantite
        });
    });

    return produits;
}

$(document).ready(function () {
    var produitId = $('#produitId').val();
    var produitCommand = localStorage.getItem('command') !== null ? localStorage.getItem('command') : "{}";
    produitCommand = JSON.parse(produitCommand);
    if (produitCommand['prod'+produitId] !== undefined) {
        $('#nbCommand').val(produitCommand['prod'+produitId].quantite);
    }
});
$('#panier').on('click', function () {
    var produitId = $('#produitId').val();
    var produitCommand = localStorage.getItem('command') !== null ? localStorage.getItem('command') : "{}";
    produitCommand = JSON.parse(produitCommand);
    if (produitCommand['prod'+produitId] === undefined) {
        produitCommand['prod'+produitId] = {
            'id': produitId,
            'titre': $('#titre').text(),
            'description': $('#description').text(),
            'prix': $('#prix').text(),
            'quantite': $('#nbCommand').val()
        };
    } else {
        produitCommand['prod'+produitId].quantite = $('#nbCommand').val();
    }

    localStorage.setItem('command', JSON.stringify(produitCommand));
    calcSomme();
});

function calcSomme(){
    var command = localStorage.getItem('command');
    var total = 0;

    if (command !== null) {
        command = JSON.parse(command);
        $.each(command, function (index ,c) {
            total += c.quantite * c.prix;
        });
    }

    localStorage.setItem('total', total);
}

$(document).on('click', '.voirFiche', function(){
    location.href = Routing.generate('_challenge_front_fiche_produit', {'id': $(this).data('id')});
});

$(document).ready(function(){
    $('#list-produit').DataTable({
        "processing": true,
        "serverSide": false,
        "searching": true,
        "paging": true,
        "info": false,
        "ajax": {
            "url": Routing.generate('_challenge_get_produits'),
            "type": "POST"
        },
        order: [],
        "pageLength": 10,
        "bLengthChange": false,
        "autoWidth": false,
        "columns": [
            {"data": "titre"},
            {"data": "description"},
            {"data": "prix"},
            {
                "targets": 0,
                "searchable": false,
                "orderable": false,
                "render": function(data, type, row){
                    return '<a href="#" class="voirFiche" data-id="'+row.id+'">Voir fiche</a>';
                }
            }
        ]
    });
});

$('#voirDetailPanier').on('click', function() {
    location.href = Routing.generate('_challenge_front_voir_panier');
});