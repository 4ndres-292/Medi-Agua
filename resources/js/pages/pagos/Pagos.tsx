import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface Pago {
    id: number;
    factura_id: number;
    monto: string;
    metodo_pago: string;
    fecha_pago: string;
}

const Pagos: React.FC = () => {
    const [pagos, setPagos] = useState<Pago[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/pagos')
            .then(res => setPagos(res.data.data))
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    }, []);

    return (
        <Layout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Pagos</h1>
                <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    + Nuevo Pago
                </button>
            </div>

            <div className="bg-white rounded-lg shadow overflow-hidden">
                {loading ? (
                    <div className="p-8 text-center text-gray-500">Cargando...</div>
                ) : (
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 border-b">
                            <tr>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">ID</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Factura</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Monto</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Método</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Fecha</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {pagos.map(pago => (
                                <tr key={pago.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 text-sm text-gray-900">{pago.id}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{pago.factura_id}</td>
                                    <td className="px-6 py-4 text-sm text-gray-900 font-medium">Bs. {pago.monto}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{pago.metodo_pago}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{pago.fecha_pago}</td>
                                    <td className="px-6 py-4 text-sm">
                                        <button className="text-blue-600 hover:text-blue-800 mr-3">Ver</button>
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

export default Pagos;
