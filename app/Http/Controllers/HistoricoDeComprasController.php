<?php

namespace App\Http\Controllers;

use App\Models\HistoricoDeCompra;
use Illuminate\Http\Request;

class HistoricoDeComprasController extends Controller
{

    public function update($id, $comprovante_de_pagamento, $status){
        $compra = HistoricoDeCompra::find($id);
        $compra->comprovante_de_pagamento = $comprovante_de_pagamento;
        $compra->status_do_pagamento = $status;
        $compra->save();

        return response()->json($compra);
    }

    public function destroy($id){
        $compra = HistoricoDeCompra::find($id);
        $compra->delete();

        return response()->json(null, 204);
    }
}
