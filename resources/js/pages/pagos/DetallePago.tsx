import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface PagoDetalle {
    id: number;
    monto: string;
    metodo_pago: string;
    referencia_qr: string | null;
    fecha_pago: string;
    factura?: {
        numero: string;
        estado: string;
        socio?: { nombres: string; apellidos: string };
    };
}

const DetallePago: React.FC = () => {
    const { id } = useParams();
    const [pago, setPago] = useState<PagoDetalle | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get(`/pagos/${id}`)
            .then(res => setPago(res.data.data))
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    }, [id]);

    if (loading) {
        return <Layout><div className="p-8 text-center text-gray-500">Cargando...</div></Layout>;
    }

    if (!pago) {
        return <Layout><div className="p-8 text-center text-gray-500">Pago no encontrado.</div></Layout>;
    }

    return (
        <Layout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Pago #{pago.id}</h1>
                <Link to="/pagos" className="text-blue-600 hover:underline">← Volver</Link>
            </div>

            <div className="bg-white rounded-lg shadow p-6 space-y-3 max-w-lg">
                <p><strong>Factura:</strong> N° {pago.factura?.numero}</p>
                <p><strong>Socio:</strong> {pago.factura?.socio?.nombres} {pago.factura?.socio?.apellidos}</p>
                <p><strong>Monto pagado:</strong> Bs. {pago.monto}</p>
                <p><strong>Método:</strong> {pago.metodo_pago}</p>
                {pago.referencia_qr && <p><strong>Referencia:</strong> {pago.referencia_qr}</p>}
                <p><strong>Fecha de pago:</strong> {pago.fecha_pago}</p>
                <p><strong>Estado de la factura:</strong> {pago.factura?.estado}</p>
            </div>
        </Layout>
    );
};

export default DetallePago;