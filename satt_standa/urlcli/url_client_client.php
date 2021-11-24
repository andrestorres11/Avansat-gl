<?php
class UrlClientes {
    public function __construct() {
        $this->redireccionar();
    }

    private function redireccionar(){
        $this->agregarEncabezado();
        echo "
				<table style='width: 100%;'>
					<tr>
						<td>
							<br>
							".$this->contenido()."
						</td>
					</tr>
				</table>
			";
        echo "<script>
        window.open('https://corona.intrared.net/ap/satt_corona/con_pedido/');
              </script>
        ";
    }

    private function agregarEncabezado() {
        echo '
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	        	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	        	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
	         	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
                <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">   
	        ';
    }

    private function contenido(){
        return ' <div class="row" style="margin: 40px;">
        <div class="col-md-12 border">
            <div class="row m-3">
                <div class="col-md-12 p-2">
                    <h4>En caso de que la aplicación no lo redireccione a la URL de consulta de pedidos, deberá habilitar la ventana emergente del navegador o dar clic en el siguiente botón:</h4>
                </div>
            </div>
            <div class="row m-3">
                <div class="col-md-12 p-2 text-center">
                    <a href="https://corona.intrared.net/ap/satt_corona/con_pedido/" class="btn btn-primary" target="_blank"><i class="fas fa-link"></i> Url Clientes</a>
                </div>
            </div>
        </div>
    </div>';
    }
}

new UrlClientes();

?>