import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

const Deudores: React.FC = () => {
    const [data, setData] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/reportes/deudores')
            .then(res => setData(res.data.data || []))
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    }, []);

    return (
        <Layout>
            <h1 className="text-3xl font-bold text-gray-800 mb-6">Reporte de Deudores</h1>

            <div className="bg-white rounded-lg shadow p-6">
                <div className="flex items-center justify-between mb-4">
                    <h2 className="text-xl font-semibold text-gray-700">Socios con Deudas Pendientes</h2>
                    <button className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Exportar PDF
                    </button>
                </div>

                {loading ? (
                    <div className="p-8 text-center text-gray-500">Cargando datos...</div>
                ) : data.length === 0 ? (
                    <div className="p-8 text-center text-gray-400">
                        No hay deudores registrados
                    </div>
                ) : (
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 border-b">
                            <tr>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Socio</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Factura</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Monto</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Vencimiento</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Días Vencida</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {data.map((item, index) => (
                                <tr key={index} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 text-sm text-gray-900">{item.socio}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{item.factura}</td>
                                    <td className="px-6 py-4 text-sm text-red-600 font-medium">Bs. {item.monto}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{item.vencimiento}</td>
                                    <td className="px-6 py-4 text-sm">
                                        <span className="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                            {item.dias} días
                                        </span>
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

export default Deudores;
