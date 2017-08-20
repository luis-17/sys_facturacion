<?php

class Model_cliente_persona extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function m_cargar_cliente_persona($paramPaginate)
    {
        $this->db->select("co.idcolaborador, CONCAT(co.nombres, ' ', co.apellidos) As colaborador", false);
        $this->db->select('cp.idclientepersona, cp.num_documento, cp.nombres, cp.apellidos, cp.sexo, cp.telefono_fijo, cp.telefono_movil, cp.email, cp.fecha_nacimiento, 
			cc.idcategoriacliente, cc.descripcion_cc, tdc.idtipodocumentocliente, tdc.descripcion_tdc');
        $this->db->from('cliente_persona cp');
        $this->db->join('categoria_cliente cc', 'cp.idcategoriacliente = cc.idcategoriacliente');
        $this->db->join('tipo_documento_cliente tdc', 'cp.idtipodocumentocliente = tdc.idtipodocumentocliente');
        $this->db->join('colaborador co', 'cp.idcolaborador = co.idcolaborador', 'left');
        $this->db->where('cp.estado_cl', 1);
        $this->db->where('cp.idempresaadmin', $this->sessionFactur['idempresaadmin']);
        if (isset($paramPaginate['search']) && $paramPaginate['search']) {
            foreach ($paramPaginate['searchColumn'] as $key => $value) {
                if (!empty($value)) {
                    $this->db->like($key, strtoupper_total($value), false);
                }
            }
        }

        if ($paramPaginate['sortName']) {
            $this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
        }
        if ($paramPaginate['firstRow'] || $paramPaginate['pageSize']) {
            $this->db->limit($paramPaginate['pageSize'], $paramPaginate['firstRow']);
        }

        return $this->db->get()->result_array();
    }

    public function m_count_cliente_persona($paramPaginate = false)
    {
        $this->db->select('COUNT(*) AS contador');
        $this->db->from('cliente_persona cp');
        $this->db->join('categoria_cliente cc', 'cp.idcategoriacliente = cc.idcategoriacliente');
        $this->db->join('tipo_documento_cliente tdc', 'cp.idtipodocumentocliente = tdc.idtipodocumentocliente');
        $this->db->where('cp.estado_cl', 1);
        $this->db->where('cp.idempresaadmin', $this->sessionFactur['idempresaadmin']);
        if (isset($paramPaginate['search']) && $paramPaginate['search']) {
            foreach ($paramPaginate['searchColumn'] as $key => $value) {
                if (!empty($value)) {
                    $this->db->like($key, strtoupper_total($value), false);
                }
            }
        }
        $fData = $this->db->get()->row_array();

        return $fData;
    }

    // VALIDACIONES
    public function m_validar_cliente_persona_num_documento($numDocumento, $excepcion = false, $idclientepersona = null)
    {
        $this->db->select('cp.idclientepersona');
        $this->db->from('cliente_persona cp');
        $this->db->where('cp.estado_cl', 1);
        $this->db->where('cp.num_documento', $numDocumento);
        $this->db->where('cp.idempresaadmin', $this->sessionFactur['idempresaadmin']);
        if ($excepcion) {
            $this->db->where_not_in('cp.idclientepersona', $idclientepersona);
        }
        $this->db->limit(1);

        return $this->db->get()->result_array();
    }

    public function m_registrar($datos)
    {
        $data = [
            'idtipodocumentocliente' => 1, // DNI
            'idcategoriacliente' => $datos['categoria_cliente']['id'],
            'num_documento' => $datos['num_documento'],
            'nombres' => strtoupper($datos['nombres']),
            'apellidos' => strtoupper($datos['apellidos']),
            'sexo' => $datos['sexo']['id'],
            'telefono_movil' => $datos['telefono_movil'],
            'telefono_fijo' => empty($datos['telefono_fijo']) ? null : $datos['telefono_fijo'],
            'email' => empty($datos['email']) ? null : strtoupper($datos['email']),
            'fecha_nacimiento' => empty($datos['fecha_nacimiento']) ? null : darFormatoYMD($datos['fecha_nacimiento']),
            'idempresaadmin' => $this->sessionFactur['idempresaadmin'],
            'idcolaborador' => empty($datos['colaborador']['id']) ? null : $datos['colaborador']['id'],
            'createdat' => date('Y-m-d H:i:s'),
            'updatedat' => date('Y-m-d H:i:s'),
        ];

        return $this->db->insert('cliente_persona', $data);
    }

    public function m_editar($datos)
    {
        $data = [
            'idtipodocumentocliente' => 1, // DNI
            'idcategoriacliente' => $datos['categoria_cliente']['id'],
            'idcolaborador' => empty($datos['colaborador']['id']) ? null : $datos['colaborador']['id'],
            'num_documento' => $datos['num_documento'],
            'nombres' => strtoupper($datos['nombres']),
            'apellidos' => strtoupper($datos['apellidos']),
            'sexo' => $datos['sexo']['id'],
            'telefono_movil' => $datos['telefono_movil'],
            'telefono_fijo' => empty($datos['telefono_fijo']) ? null : $datos['telefono_fijo'],
            'email' => empty($datos['email']) ? null : strtoupper($datos['email']),
            'fecha_nacimiento' => empty($datos['fecha_nacimiento']) ? null : darFormatoYMD($datos['fecha_nacimiento']),
            'updatedat' => date('Y-m-d H:i:s'),
        ];
        $this->db->where('idclientepersona', $datos['id']);

        return $this->db->update('cliente_persona', $data);
    }

    public function m_anular($datos)
    {
        $data = [
            'estado_cl' => 0,
            'updatedat' => date('Y-m-d H:i:s'),
        ];
        $this->db->where('idclientepersona', $datos['id']);

        return $this->db->update('cliente_persona', $data);
    }
}

?>