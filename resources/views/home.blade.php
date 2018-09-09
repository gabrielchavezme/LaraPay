@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (!empty($success))
                    <div class="alert alert-success" role="alert">
                        {{ $success }}
                    </div>
                    @elseif(!empty($error))
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>

                    @endif
                    <form action="/process" method="POST" id="payment-form">
                        <input type="hidden" name="token_id" id="token_id">
                        @csrf
                        <h2>Detalles del pago</h2>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Nombre del cliente</label>
                                    <input class="form-control" type="text" name="nombre" placeholder="Nombre del cliente">
                                </div>
                                <div class="col-md-6">
                                    <label>Apellidos del cliente</label>
                                    <input class="form-control" type="text" name="apellidos" placeholder="Apellidos">
                                </div>
                                <div class="col-md-6">
                                    <label>Correo</label>
                                    <input class="form-control" type="email" name="correo" placeholder="Correo">
                                </div>
                                <div class="col-md-6">
                                    <label>Telefono</label>
                                    <input class="form-control" type="text" name="telefono" placeholder="Ingresa el telefono">
                                </div>
                                <div class="col-md-12">
                                    <label>Ingresa el monto a cobrar</label>
                                    <input class="form-control" type="number" maxlength="4" name="amount" placeholder="Ingresa el monto a cobrar">
                                </div>
                            </div>
                        </div>
                        <h2>Detalles de la tarjeta</h2>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Nombre del titular</label>
                                    <input class="form-control" type="text" placeholder="Como aparece en la tarjeta" autocomplete="off" data-openpay-card="holder_name">
                                </div>
                                <div class="col-md-6">
                                    <label>Número de tarjeta</label>
                                    <input class="form-control" type="text" autocomplete="off" maxlength="16" data-openpay-card="card_number">
                                </div>
                                <div class="col-md-12" style="margin-top: 20px">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Mes</label>
                                            <input class="form-control" type="text" placeholder="Mes" data-openpay-card="expiration_month">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Año</label>
                                            <input class="form-control" type="text" placeholder="Año" data-openpay-card="expiration_year">
                                        </div>
                                        <div class="col-md-4">
                                            <label>CCV</label>
                                            <input class="form-control" type="text" placeholder="3 dígitos" autocomplete="off" data-openpay-card="cvv2">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="margin-top: 20px;color:white">
                                    <a id="pay-button" class="btn btn-primary">Pagar ahora</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript" 
src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" 
src="https://openpay.s3.amazonaws.com/openpay.v1.min.js"></script>
<script type='text/javascript' 
src="https://openpay.s3.amazonaws.com/openpay-data.v1.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        OpenPay.setId('mm7qw0anaiflqf3no0qi');
        OpenPay.setApiKey('pk_690723827a6d4bcf9748778f425f8a63');
        OpenPay.setSandboxMode(true);
            //Se genera el id de dispositivo
            var deviceSessionId = OpenPay.deviceData.setup("payment-form", "deviceIdHiddenFieldName");
            
            $('#pay-button').on('click', function(event) {
                event.preventDefault();
                $("#pay-button").prop( "disabled", true);
                OpenPay.token.extractFormAndCreate('payment-form', sucess_callbak, error_callbak);                
            });

            var sucess_callbak = function(response) {
              var token_id = response.data.id;
              $('#token_id').val(token_id);
              $('#payment-form').submit();
          };

          var error_callbak = function(response) {
            var desc = response.data.description != undefined ? response.data.description : response.message;
            alert("ERROR [" + response.status + "] " + desc);
            $("#pay-button").prop("disabled", false);
        };

    });
</script>
@endsection
