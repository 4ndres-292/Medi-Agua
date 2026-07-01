import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface Factura {
    id: number;
    numero: string;
    socio_id: number;
    monto_total: string;
    fecha_emision: string;
    fecha_vencimiento: string;
    estado: string;
}

const Facturas: React.FC = () => {
    const [facturas, setFacturas] = useState<Factura[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/facturas')
            .then(res => setFacturas(res.data.data))
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    }, []);

    const getEstadoColor = (estado: string) => {
        switch (estado) {
            case 'Pagada': return 'bg-green-100 text-green-800';
            case 'Pendiente': return 'bg-yellow-100 text-yellow-800';
            case 'Vencida': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <Layout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Facturas</h1>
                <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    + Nueva Factura
                </button>
            </div>

            <div className="bg-white rounded-lg shadow overflow-hidden">
                {loading ? (
                    <div className="p-8 text-center text-gray-500">Cargando...</div>
                ) : (
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 border-b">
                            <tr>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">N°</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Socio</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Monto</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Emisión</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Vencimiento</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Estado</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {facturas.map(factura => (
                                <tr key={factura.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 text-sm text-gray-900">{factura.numero}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{factura.socio_id}</td>
                                    <td className="px-6 py-4 text-sm text-gray-900 font-medium">Bs. {factura.monto_total}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{factura.fecha_emision}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{factura.fecha_vencimiento}</td>
                                    <td className="px-6 py-4 text-sm">
                                        <span className={`px-2 py-1 rounded-full text-xs ${getEstadoColor(factura.estado)}`}>
                                            {factura.estado}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-sm">
                                        <button className="text-blue-600 hover:text-blue-800 mr-3">Ver</button>
                                        <button className="text-red-600 hover:text-red-800">Anular</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </Layout>
    );
};

export default Facturas;
