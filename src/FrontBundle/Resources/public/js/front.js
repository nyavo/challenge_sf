$(document).ready(function(){
    createTablePanier();
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
            "quantite": value.quantite,
            "total": value.total
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
    var nbCommand = $('#nbCommand').val() == '' ? 0 : $('#nbCommand').val();
    if (nbCommand > 0) {
        if (produitCommand['prod' + produitId] === undefined) {
            produitCommand['prod' + produitId] = {
                'id': produitId,
                'titre': $('#titre').text(),
                'description': $('#description').text(),
                'prix': $('#prix').text(),
                'quantite': nbCommand,
                'total': parseInt($('#prix').text()) * nbCommand
            };
        } else {
            produitCommand['prod' + produitId].quantite = nbCommand;
            produitCommand['prod' + produitId].total = parseInt($('#prix').text()) * nbCommand;
        }
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

$(document).on('click', '.supprimerPanier', function(){
    var produitCommand = JSON.parse(localStorage.getItem('command'));

    delete produitCommand['prod'+$(this).data('id')];

    localStorage.setItem('command', JSON.stringify(produitCommand));
    calcSomme();
    createTablePanier();
});

function createTablePanier()
{
    $('#panier-list').DataTable({
        "processing": true,
        "serverSide": false,
        "searching": true,
        "paging": true,
        "info": false,
        "destroy": true,
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
            {"data": "total"},
            {
                "targets": 0,
                "searchable": false,
                "orderable": false,
                "render": function(data, type, row){
                    // return '<div class="btn btn-danger btn-sm voirFiche" data-id="'+row.id+'"><i class="fa fa-edit"></i></div>';
                    return '<a href="#" class="voirFiche" data-id="'+row.id+'"><i class="fa fa-edit"></i></a>' +
                        '<input type="hidden" name="produits['+row.id+']" value="'+row.quantite+'">';
                }
            },
            {
                "targets": 0,
                "searchable": false,
                "orderable": false,
                "render": function(data, type, row){
                    // return '<div class="btn btn-danger btn-sm voirFiche" data-id="'+row.id+'"><i class="fa fa-edit"></i></div>';
                    return '<a href="#" class="supprimerPanier" data-id="'+row.id+'"><i class="fa fa-trash-o"></i></a>';
                }
            }
        ]
    });

    var total = localStorage.getItem('total') !== null ? localStorage.getItem('total') : 0;

    $('#total').text(total);
    $('#command_montantTotal').val(total);
    $('#totalBloc').show();
}

$('#confirmCommand').on('click', function(){
    $.ajax({
        type: "POST",
        url: Routing.generate('_challenge_front_panier_save'),
        data: $("#savePanierForm").serialize(),
        success: function(data)
        {
            if (data.success) {
                localStorage.removeItem('command');
                localStorage.removeItem('total');
                location.href = Routing.generate('_challenge_front_commande');
            } else {
                location.href = Routing.generate('_challenge_front_voir_panier');
            }
        }
    });
});

$(document).ready(function() {
    $('#list-commande').DataTable({
        "processing": true,
        "serverSide": false,
        "searching": true,
        "paging": true,
        "info": false,
        "ajax": {
            "url": Routing.generate('_challenge_front_commande_list'),
            "type": "POST"
        },
        order: [],
        "pageLength": 10,
        "bLengthChange": false,
        "autoWidth": false,
        "columns": [
            {"data": "id"},
            {
                "data": "date",
                "render": function(d) {
                    var dateFlux = new Date(d.date);

                    return ("0" + dateFlux.getDate()).slice(-2) + "-" + ("0"+(dateFlux.getMonth()+1)).slice(-2) + "-" +
                        dateFlux.getFullYear() + " " + ("0" + dateFlux.getHours()).slice(-2) + ":" + ("0" + dateFlux.getMinutes()).slice(-2);
                }
            },
            {"data": "client"},
            {"data": "email"},
            {"data": "montantTotal"},
            {
                "targets": 0,
                "searchable": false,
                "orderable": false,
                "render": function(data, type, row){
                    return '<a href="#" class="detailCommande" data-id="'+row.id+'">Détail</a>';
                }
            }
        ]
    });
});

$(document).on('click', '.detailCommande', function(){
    location.href = Routing.generate('_challenge_front_commande_detail', {'id': $(this).data('id')});
});

$(document).ready(function(){
    $('#command-produits-list').DataTable({
        "processing": true,
        "serverSide": false,
        "searching": true,
        "paging": true,
        "info": false,
        "ajax": {
            "url": Routing.generate('_challenge_front_commande_produits', {"id": $('#commandId').val()}),
        },
    order: [],
        "pageLength": 10,
        "bLengthChange": false,
        "autoWidth": false,
        "columns": [
            { "data": "titre" },
            { "data": "quantite" },
            { "data": "prix" },
            { "data": "total" }
        ]
    });
});
