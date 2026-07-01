import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface Tarifa {
    id: number;
    nombre: string;
    precio: string;
}

const Tarifas: React.FC = () => {
    const [tarifas, setTarifas] = useState<Tarifa[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/tarifas')
            .then(res => setTarifas(res.data.data))
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    }, []);

    return (
        <Layout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Tarifas</h1>
                <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    + Nueva Tarifa
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
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Nombre</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Precio</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {tarifas.map(tarifa => (
                                <tr key={tarifa.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 text-sm text-gray-900">{tarifa.id}</td>
                                    <td className="px-6 py-4 text-sm text-gray-900">{tarifa.nombre}</td>
                                    <td className="px-6 py-4 text-sm text-gray-900 font-medium">Bs. {tarifa.precio}</td>
                                    <td className="px-6 py-4 text-sm">
                                        <button className="text-blue-600 hover:text-blue-800 mr-3">Editar</button>
                                        <button className="text-red-600 hover:text-red-800">Eliminar</button>
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

export default Tarifas;
