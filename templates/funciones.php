<?php

    function validarEntero($valor, $campo) {
        if (!is_numeric($valor)) {
            throw new Exception("Error en $campo: valor inválido");
        }
        return intval($valor);
    }

    function validarTexto($valor, $campo) {
        if (!is_string($valor) || trim($valor)==='') {
            throw new Exception("Error en $campo: texto inválido");
        }
        return trim($valor);
    }

?>