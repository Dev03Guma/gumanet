<script>
$(document).ready(function() {
    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Ventas</li>`);

    $("#tblClientes_length").hide();
    $("#tblClientes_filter").hide();

	ventasGraf = {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie',
			renderTo: 'container01'
		},
		title: {
			
		},
		subtitle: {
			
		},
        tooltip: {
            headerFormat: '<span style="font-size:11px">Ventas</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>C${point.y:,.2f}</b>',
            shared: true,
            useHTML: true
        },
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false
				},
				showInLegend: true
			}
		},
		series: [{
            name: 'MONTO',
            data: []
		}]
	};
    dataVentasClientes(false);
    dataVentasArticulos(false);
});

$('#btnSearchArt').on('keyup', function() {
    var table = $('#tblArticulos').DataTable();
    table.search(this.value).draw();
});

$('#btnSearchCl').on('keyup', function() {
    var table = $('#tblClientes').DataTable();
    table.search(this.value).draw();
});

$("#filterData").click( function() {
    $.ajax({
        type: "POST",
        url: "ventasDetalle",
        data:{
            clase 		: $("#cmbClase option:selected").text(),
            cliente 	: $("#cmbCliente option:selected").val(),
            articulo 	: $("#cmbArticulo option:selected").val(),
            mes 		: $('#cmbMes option:selected').val(),
            anio 		: $('#cmbAnio option:selected').val()
        },
        success: function (json) {
			if (json) {
				dataVentasClientes(json);
				dataVentasArticulos(json);
			}else {
				mensaje("No se encontraron registros que coincidan con la busqueda", "error")
				$("#MontoMeta").text('0.00');
				$("#MontoMeta2").text('0.00');
				$('#tblClientes').DataTable()
				.clear()
				.draw();

				$('#tblArticulos').DataTable()
				.clear()
				.draw();
			}
        }
    });
})

function dataVentasClientes(json) {
	$('#tblClientes').DataTable ( {
		"data":json,
		"destroy": true,
		"info":    false,
		"lengthMenu": [[5,10,-1], [5,10,"Todo"]],
		"language": {
			"zeroRecords": "Cargando...",
			"paginate": {
				"first":      "Primera",
				"last":       "Última ",
				"next":       "Siguiente",
				"previous":   "Anterior"
			},
			"lengthMenu": "MOSTRAR _MENU_",
			"emptyTable": "Aún no ha realizado ninguna busqueda",
			"search":     "BUSCAR"
		},
		'columns': [
			{ "data": "cliente" },
			{ "data": "nombre" },
			{ "data": "factura" },
			{ "data": "fecha02" },
			{ "data": "Monto", render: $.fn.dataTable.render.number( ',', '.', 2 ) }
		],
		"columnDefs": [
			{"className": "text-right", "targets": [ 4 ]},
			{"className": "text-center", "targets": [ 0, 2, 3 ]},
			{ "width": "30%", "targets": [ 1 ] },
			{ "width": "5%", "targets": [ 0, 2, 3, 4 ] }
		],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            $('#MontoMeta').text('C$'+ numeral(total).format('0,0.00'));
        },
		"fnInitComplete": function () {
			$("#tblClientes_length").hide();
			$("#tblClientes_filter").hide();
		}
	});
}

function dataVentasArticulos(json) {
	table = $('#tblArticulos').DataTable ( {
				"data":json,
				"destroy": true,
				"info":    false,
				"lengthMenu": [[5,10,-1], [5,10,"Todo"]],
				"language": {
					"zeroRecords": "Cargando...",
					"paginate": {
						"first":      "Primera",
						"last":       "Última ",
						"next":       "Siguiente",
						"previous":   "Anterior"
					},
					"lengthMenu": "MOSTRAR _MENU_",
					"emptyTable": "Aún no ha realizado ninguna busqueda",
					"search":     "BUSCAR"
				},
				'columns': [
					{ "data": "articulo" },
					{ "data": "descripcion" },
					{ "data": "Cantidad", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
					{ "data": "precioUnitario", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
					{ "data": "total", render: $.fn.dataTable.render.number( ',', '.', 2 ) }
				],
				"columnDefs": [
					{"className": "text-right", "targets": [ 2, 3, 4 ]},
					{"className": "text-center", "targets": [ 0 ]},
					{ "width": "30%", "targets": [ 1 ] },
					{ "width": "5%", "targets": [ 0, 2, 3, 4 ] }
				],
		        "footerCallback": function ( row, data, start, end, display ) {
		            var api = this.api(), data;
		            var intVal = function ( i ) {
		                return typeof i === 'string' ?
		                    i.replace(/[\$,]/g, '')*1 :
		                    typeof i === 'number' ?
		                        i : 0;
		            };
		            mta = 0;
		            total = api
		                .column( 4 )
		                .data()
		                .reduce( function (a, b) {
		                    return intVal(a) + intVal(b);
		                }, 0 );
		            $('#MontoMeta2').text('C$'+ numeral(total).format('0,0.00'));

		            dta = [{name: 'Real', y: total},{name:'Meta', y:mta}]
		            subtitle= (total==0)?'':($("#cmbClase option:selected").text());
		            graficaVentas(dta, subtitle)
		        },
				"fnInitComplete": function () {
					$("#tblArticulos_length").hide();
					$("#tblArticulos_filter").hide();
				}
			});
}

var ventasGraf = {};
function graficaVentas(data, subtitle) {    
	ventasGraf.series[0].data = data;
	ventasGraf.title.text = 'Clase Terapeutica';
	ventasGraf.subtitle.text = subtitle;
	chart = new Highcharts.Chart(ventasGraf);	
}
</script>