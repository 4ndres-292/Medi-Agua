import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface Lectura {
    id: number;
    medidor_id: number;
    lectura_actual: string;
    medidor?: { codigo: string; socio_id: number; socio?: { nombres: string; apellidos: string } };
}

interface Tarifa {
    id: number;
    nombre: string;
    precio: string;
}

interface LineaTarifa {
    tarifa_id: number;
    nombre: string;
    precio: number;
    cantidad: number;
    seleccionada: boolean;
}

const NuevaFactura: React.FC = () => {
    const navigate = useNavigate();

    const [lecturas, setLecturas] = useState<Lectura[]>([]);
    const [tarifas, setTarifas] = useState<Tarifa[]>([]);
    const [lecturaId, setLecturaId] = useState<string>('');
    const [fechaVencimiento, setFechaVencimiento] = useState<string>('');
    const [lineas, setLineas] = useState<LineaTarifa[]>([]);
    const [enviando, setEnviando] = useState(false);
    const [error, setError] = useState<string>('');

    useEffect(() => {
        api.get('/lecturas')
            .then(res => {
                const payload = res.data.data;
                const lista = Array.isArray(payload) ? payload : payload?.data ?? [];
                setLecturas(lista);
            })
            .catch(err => console.error(err));

        api.get('/tarifas')
            .then(res => {
                const payload = res.data.data;
                const lista: Tarifa[] = Array.isArray(payload) ? payload : payload?.data ?? [];
                setTarifas(lista);
                setLineas(lista.map(t => ({
                    tarifa_id: t.id,
                    nombre: t.nombre,
                    precio: Number(t.precio),
                    cantidad: 1,
                    seleccionada: true,
                })));
            })
            .catch(err => console.error(err));
    }, []);

    const toggleLinea = (tarifaId: number) => {
        setLineas(prev => prev.map(l =>
            l.tarifa_id === tarifaId ? { ...l, seleccionada: !l.seleccionada } : l
        ));
    };

    const cambiarCantidad = (tarifaId: number, cantidad: number) => {
        setLineas(prev => prev.map(l =>
            l.tarifa_id === tarifaId ? { ...l, cantidad } : l
        ));
    };

    const totalEstimado = lineas
        .filter(l => l.seleccionada)
        .reduce((sum, l) => sum + (l.precio * l.cantidad), 0);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        const seleccionadas = lineas.filter(l => l.seleccionada);

        if (!lecturaId) {
            setError('Selecciona una lectura.');
            return;
        }
        if (seleccionadas.length === 0) {
            setError('Selecciona al menos una tarifa.');
            return;
        }
        if (!fechaVencimiento) {
            setError('Indica la fecha de vencimiento.');
            return;
        }

        const lecturaElegida = lecturas.find(l => l.id === Number(lecturaId));

        if (!lecturaElegida?.medidor?.socio_id) {
            setError('No se pudo determinar el socio de esta lectura.');
            return;
        }

        setEnviando(true);
        try {
            await api.post('/facturas', {
                socio_id: lecturaElegida.medidor.socio_id,
                lectura_id: Number(lecturaId),
                fecha_vencimiento: fechaVencimiento,
                tarifas: seleccionadas.map(l => ({
                    tarifa_id: l.tarifa_id,
                    cantidad: l.cantidad,
                })),
            });
            navigate('/facturas');
        } catch (err: any) {
            console.error(err);
            setError(err.response?.data?.message ?? 'Error al crear la factura.');
        } finally {
            setEnviando(false);
        }
    };

    return (
        <Layout>
            <h1 className="text-3xl font-bold text-gray-800 mb-6">Nueva Factura</h1>

            <form onSubmit={handleSubmit} className="bg-white rounded-lg shadow p-6 space-y-6 max-w-2xl">

                {error && (
                    <div className="bg-red-50 text-red-700 px-4 py-2 rounded">{error}</div>
                )}

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Lectura</label>
                    <select
                        value={lecturaId}
                        onChange={e => setLecturaId(e.target.value)}
                        className="w-full border rounded-lg px-3 py-2"
                    >
                        <option value="">Selecciona una lectura...</option>
                        {lecturas.map(l => (
                            <option key={l.id} value={l.id}>
                                Medidor {l.medidor?.codigo ?? l.medidor_id} — {l.medidor?.socio?.nombres} {l.medidor?.socio?.apellidos} (lect. {l.lectura_actual})
                            </option>
                        ))}
                    </select>
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Fecha de vencimiento</label>
                    <input
                        type="date"
                        value={fechaVencimiento}
                        onChange={e => setFechaVencimiento(e.target.value)}
                        className="w-full border rounded-lg px-3 py-2"
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Conceptos a cobrar</label>
                    <div className="space-y-2">
                        {lineas.map(l => (
                            <div key={l.tarifa_id} className="flex items-center gap-3 border rounded-lg px-3 py-2">
                                <input
                                    type="checkbox"
                                    checked={l.seleccionada}
                                    onChange={() => toggleLinea(l.tarifa_id)}
                                />
                                <span className="flex-1">{l.nombre} <span className="text-gray-400">(Bs. {l.precio})</span></span>
                                <input
                                    type="number"
                                    min={0.01}
                                    step={0.01}
                                    value={l.cantidad}
                                    disabled={!l.seleccionada}
                                    onChange={e => cambiarCantidad(l.tarifa_id, Number(e.target.value))}
                                    className="w-24 border rounded px-2 py-1 disabled:bg-gray-100"
                                />
                                <span className="w-24 text-right text-sm text-gray-600">
                                    Bs. {(l.precio * l.cantidad).toFixed(2)}
                                </span>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="flex items-center justify-between border-t pt-4">
                    <span className="text-lg font-bold">Total estimado: Bs. {totalEstimado.toFixed(2)}</span>
                    <div className="flex gap-3">
                        <button
                            type="button"
                            onClick={() => navigate('/facturas')}
                            className="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            disabled={enviando}
                            className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {enviando ? 'Guardando...' : 'Guardar factura'}
                        </button>
                    </div>
                </div>

            </form>
        </Layout>
    );
};

export default NuevaFactura;