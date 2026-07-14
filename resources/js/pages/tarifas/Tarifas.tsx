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

    // Estado del modal (crear / editar)
    const [modalAbierto, setModalAbierto] = useState(false);
    const [tarifaEditando, setTarifaEditando] = useState<Tarifa | null>(null);
    const [nombre, setNombre] = useState('');
    const [precio, setPrecio] = useState('');
    const [guardando, setGuardando] = useState(false);
    const [error, setError] = useState('');

    const cargarTarifas = () => {
        setLoading(true);
        api.get('/tarifas')
            .then(res => {
                const payload = res.data.data;
                const lista = Array.isArray(payload) ? payload : payload?.data ?? [];
                setTarifas(lista);
            })
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        cargarTarifas();
    }, []);

    const abrirNuevo = () => {
        setTarifaEditando(null);
        setNombre('');
        setPrecio('');
        setError('');
        setModalAbierto(true);
    };

    const abrirEditar = (tarifa: Tarifa) => {
        setTarifaEditando(tarifa);
        setNombre(tarifa.nombre);
        setPrecio(tarifa.precio);
        setError('');
        setModalAbierto(true);
    };

    const cerrarModal = () => {
        setModalAbierto(false);
        setTarifaEditando(null);
    };

    const handleGuardar = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        if (!nombre.trim()) {
            setError('El nombre es obligatorio.');
            return;
        }
        if (!precio || Number(precio) <= 0) {
            setError('El precio debe ser mayor a 0.');
            return;
        }

        setGuardando(true);
        try {
            if (tarifaEditando) {
                // Editar
                await api.put(`/tarifas/${tarifaEditando.id}`, {
                    nombre: nombre.trim(),
                    precio: Number(precio),
                });
            } else {
                // Crear
                await api.post('/tarifas', {
                    nombre: nombre.trim(),
                    precio: Number(precio),
                });
            }
            cerrarModal();
            cargarTarifas();
        } catch (err: any) {
            console.error(err);
            setError(err.response?.data?.message ?? 'Error al guardar la tarifa.');
        } finally {
            setGuardando(false);
        }
    };

    const handleEliminar = async (tarifa: Tarifa) => {
        if (!confirm(`¿Eliminar la tarifa "${tarifa.nombre}"?`)) return;
        try {
            await api.delete(`/tarifas/${tarifa.id}`);
            setTarifas(prev => prev.filter(t => t.id !== tarifa.id));
        } catch (err: any) {
            console.error(err);
            alert(err.response?.data?.message ?? 'No se pudo eliminar la tarifa (puede que ya esté en uso en alguna factura).');
        }
    };

    return (
        <Layout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Tarifas</h1>
                <button
                    onClick={abrirNuevo}
                    className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
                >
                    + Nueva Tarifa
                </button>
            </div>

            <div className="bg-white rounded-lg shadow overflow-hidden">
                {loading ? (
                    <div className="p-8 text-center text-gray-500">Cargando...</div>
                ) : tarifas.length === 0 ? (
                    <div className="p-8 text-center text-gray-500">No hay tarifas registradas.</div>
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
                                        <button
                                            onClick={() => abrirEditar(tarifa)}
                                            className="text-blue-600 hover:text-blue-800 mr-3"
                                        >
                                            Editar
                                        </button>
                                        <button
                                            onClick={() => handleEliminar(tarifa)}
                                            className="text-red-600 hover:text-red-800"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>

            {/* MODAL crear/editar */}
            {modalAbierto && (
                <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                    <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
                        <h2 className="text-xl font-bold text-gray-800 mb-4">
                            {tarifaEditando ? 'Editar Tarifa' : 'Nueva Tarifa'}
                        </h2>

                        <form onSubmit={handleGuardar} className="space-y-4">
                            {error && (
                                <div className="bg-red-50 text-red-700 px-3 py-2 rounded text-sm">{error}</div>
                            )}

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input
                                    type="text"
                                    value={nombre}
                                    onChange={e => setNombre(e.target.value)}
                                    className="w-full border rounded-lg px-3 py-2"
                                    placeholder="Ej: Consumo por m³"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Precio (Bs.)</label>
                                <input
                                    type="number"
                                    min={0.01}
                                    step={0.01}
                                    value={precio}
                                    onChange={e => setPrecio(e.target.value)}
                                    className="w-full border rounded-lg px-3 py-2"
                                    placeholder="0.00"
                                />
                            </div>

                            <div className="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={cerrarModal}
                                    className="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    disabled={guardando}
                                    className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {guardando ? 'Guardando...' : 'Guardar'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </Layout>
    );
};

export default Tarifas;