import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface Notificacion {
    id: number;
    titulo: string;
    mensaje: string;
    leida: boolean;
    created_at: string;
}

const Notificaciones: React.FC = () => {
    const [notificaciones, setNotificaciones] = useState<Notificacion[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/notificaciones')
            .then(res => setNotificaciones(res.data.data))
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    }, []);

    return (
        <Layout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Notificaciones</h1>
                <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    + Nueva Notificación
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
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Título</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Mensaje</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Estado</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Fecha</th>
                                <th className="px-6 py-3 text-sm font-medium text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {notificaciones.map(notif => (
                                <tr key={notif.id} className={`hover:bg-gray-50 ${!notif.leida ? 'bg-blue-50' : ''}`}>
                                    <td className="px-6 py-4 text-sm text-gray-900">{notif.id}</td>
                                    <td className="px-6 py-4 text-sm text-gray-900 font-medium">{notif.titulo}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{notif.mensaje}</td>
                                    <td className="px-6 py-4 text-sm">
                                        <span className={`px-2 py-1 rounded-full text-xs ${notif.leida ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800'}`}>
                                            {notif.leida ? 'Leída' : 'Nueva'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{notif.created_at}</td>
                                    <td className="px-6 py-4 text-sm">
                                        <button className="text-blue-600 hover:text-blue-800 mr-3">Ver</button>
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

export default Notificaciones;
