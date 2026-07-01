import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface UserProfile {
    id: number;
    username: string;
    lastname: string;
    email: string;
    rol: { id: number; name: string };
}

const Perfil: React.FC = () => {
    const [user, setUser] = useState<UserProfile | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/me')
            .then(res => setUser(res.data.data))
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    }, []);

    return (
        <Layout>
            <h1 className="text-3xl font-bold text-gray-800 mb-6">Mi Perfil</h1>

            <div className="max-w-2xl">
                <div className="bg-white rounded-lg shadow p-6">
                    {loading ? (
                        <div className="p-8 text-center text-gray-500">Cargando...</div>
                    ) : user ? (
                        <div className="space-y-4">
                            <div className="flex items-center gap-4 mb-6">
                                <div className="w-20 h-20 bg-sky-500 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                                    {user.username?.charAt(0)}
                                </div>
                                <div>
                                    <h2 className="text-2xl font-bold text-gray-800">{user.username} {user.lastname}</h2>
                                    <p className="text-gray-500">{user.rol?.name}</p>
                                </div>
                            </div>

                            <div className="border-t pt-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-500">Nombre</label>
                                        <p className="text-gray-900">{user.username}</p>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-500">Apellido</label>
                                        <p className="text-gray-900">{user.lastname}</p>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-500">Email</label>
                                        <p className="text-gray-900">{user.email}</p>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-500">Rol</label>
                                        <p className="text-gray-900">{user.rol?.name}</p>
                                    </div>
                                </div>
                            </div>

                            <div className="border-t pt-4">
                                <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Editar Perfil
                                </button>
                            </div>
                        </div>
                    ) : (
                        <div className="p-8 text-center text-gray-400">No se pudo cargar el perfil</div>
                    )}
                </div>
            </div>
        </Layout>
    );
};

export default Perfil;
