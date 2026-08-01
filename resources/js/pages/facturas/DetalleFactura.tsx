import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface TarifaDetalle {
    id: number;
    nombre: string;
    pivot: { cantidad: string; precio_unitario: string; subtotal: string };
}

interface FacturaDetalle {
    id: number;
    numero: string;
    monto_total: string;
    fecha_emision: string;
    fecha_vencimiento: string;
    estado: string;
    socio?: { nombre: string; apellido: string };
    tarifas: TarifaDetalle[];
}

const DetalleFactura: React.FC = () => {
    const { id } = useParams();
    const [factura, setFactura] = useState<FacturaDetalle | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get(`/facturas/${id}`)
            .then(res => setFactura(res.data.data))
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    }, [id]);

    if (loading) {
        return <Layout><div className="p-8 text-center text-gray-500">Cargando...</div></Layout>;
    }

    if (!factura) {
        return <Layout><div className="p-8 text-center text-gray-500">Factura no encontrada.</div></Layout>;
    }

    return (
        <Layout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Factura {factura.numero}</h1>
                <Link to="/facturas" className="text-blue-600 hover:underline">← Volver</Link>
            </div>

            <div className="bg-white rounded-lg shadow p-6 space-y-4 max-w-2xl">
                <p><strong>Socio:</strong> {factura.socio?.nombre} {factura.socio?.apellido}</p>
                <p><strong>Emisión:</strong> {factura.fecha_emision}</p>
                <p><strong>Vencimiento:</strong> {factura.fecha_vencimiento}</p>
                <p><strong>Estado:</strong> {factura.estado}</p>

                <table className="w-full text-left mt-4">
                    <thead className="border-b">
                        <tr>
                            <th className="py-2 text-sm text-gray-500">Concepto</th>
                            <th className="py-2 text-sm text-gray-500">Cantidad</th>
                            <th className="py-2 text-sm text-gray-500">P. Unitario</th>
                            <th className="py-2 text-sm text-gray-500">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {factura.tarifas.map(t => (
                            <tr key={t.id}>
                                <td className="py-2">{t.nombre}</td>
                                <td className="py-2">{t.pivot.cantidad}</td>
                                <td className="py-2">Bs. {t.pivot.precio_unitario}</td>
                                <td className="py-2">Bs. {t.pivot.subtotal}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                <div className="border-t pt-4 text-right text-lg font-bold">
                    Total: Bs. {factura.monto_total}
                </div>
            </div>
        </Layout>
    );
};

export default DetalleFactura;