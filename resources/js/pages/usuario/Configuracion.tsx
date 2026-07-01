import React from 'react';
import Layout from '../../components/layout/Layout';

const Configuracion: React.FC = () => {
    return (
        <Layout>
            <h1 className="text-3xl font-bold text-gray-800 mb-6">Configuración</h1>

            <div className="max-w-2xl space-y-6">
                <div className="bg-white rounded-lg shadow p-6">
                    <h2 className="text-xl font-semibold text-gray-700 mb-4">Cambiar Contraseña</h2>
                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                            <input
                                type="password"
                                className="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                            <input
                                type="password"
                                className="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                            <input
                                type="password"
                                className="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Actualizar Contraseña
                        </button>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow p-6">
                    <h2 className="text-xl font-semibold text-gray-700 mb-4">Preferencias</h2>
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="font-medium text-gray-700">Notificaciones por email</p>
                                <p className="text-sm text-gray-500">Recibir notificaciones importantes por correo</p>
                            </div>
                            <input type="checkbox" className="w-5 h-5 text-blue-600 rounded" />
                        </div>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="font-medium text-gray-700">Modo oscuro</p>
                                <p className="text-sm text-gray-500">Cambiar apariencia de la interfaz</p>
                            </div>
                            <input type="checkbox" className="w-5 h-5 text-blue-600 rounded" />
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow p-6">
                    <h2 className="text-xl font-semibold text-gray-700 mb-4">Sesiones Activas</h2>
                    <p className="text-gray-500 mb-4">Gestiona tus sesiones en otros dispositivos</p>
                    <button className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        Cerrar todas las sesiones
                    </button>
                </div>
            </div>
        </Layout>
    );
};

export default Configuracion;
