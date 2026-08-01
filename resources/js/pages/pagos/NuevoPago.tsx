import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface Factura {
    id: number;
    numero: string;
    monto_total: string;
    estado: string;
    socio?: { nombres: string; apellidos: string };
}

const NuevoPago: React.FC = () => {
    const navigate = useNavigate();

    const [facturas, setFacturas] = useState<Factura[]>([]);
    const [facturaId, setFacturaId] = useState('');
    const [metodoPago, setMetodoPago] = useState('Efectivo');
    const [referenciaQr, setReferenciaQr] = useState('');
    const [fechaPago, setFechaPago] = useState(new Date().toISOString().slice(0, 10));
    const [enviando, setEnviando] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        api.get('/facturas')
            .then(res => {
                const payload = res.data.data;
                const lista: Factura[] = Array.isArray(payload) ? payload : payload?.data ?? [];
                // Solo facturas que aún no están pagadas ni anuladas
                setFacturas(lista.filter(f => f.estado === 'Pendiente' || f.estado === 'Vencida'));
            })
            .catch(err => console.error(err));
    }, []);

    const facturaElegida = facturas.find(f => f.id === Number(facturaId));

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        if (!facturaId) {
            setError('Selecciona una factura.');
            return;
        }
        if (metodoPago === 'QR' && !referenciaQr.trim()) {
            setError('Ingresa la referencia del pago QR.');
            return;
        }

        setEnviando(true);
        try {
            await api.post('/pagos', {
                factura_id: Number(facturaId),
                monto: Number(facturaElegida?.monto_total ?? 0),
                metodo_pago: metodoPago,
                referencia_qr: metodoPago === 'QR' ? referenciaQr.trim() : null,
                fecha_pago: fechaPago,
            });
            navigate('/pagos');
        } catch (err: any) {
            console.error(err);
            setError(err.response?.data?.message ?? 'Error al registrar el pago.');
        } finally {
            setEnviando(false);
        }
    };

    return (
        <Layout>
            <h1 className="text-3xl font-bold text-gray-800 mb-6">Nuevo Pago</h1>

            <form onSubmit={handleSubmit} className="bg-white rounded-lg shadow p-6 space-y-5 max-w-lg">

                {error && (
                    <div className="bg-red-50 text-red-700 px-4 py-2 rounded">{error}</div>
                )}

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Factura</label>
                    <select
                        value={facturaId}
                        onChange={e => setFacturaId(e.target.value)}
                        className="w-full border rounded-lg px-3 py-2"
                    >
                        <option value="">Selecciona una factura pendiente...</option>
                        {facturas.map(f => (
                            <option key={f.id} value={f.id}>
                                N° {f.numero} — {f.socio?.nombres} {f.socio?.apellidos} — Bs. {f.monto_total} ({f.estado})
                            </option>
                        ))}
                    </select>
                    {facturas.length === 0 && (
                        <p className="text-xs text-gray-400 mt-1">No hay facturas pendientes por pagar.</p>
                    )}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Monto a pagar</label>
                    <input
                        type="text"
                        value={facturaElegida ? `Bs. ${facturaElegida.monto_total}` : ''}
                        disabled
                        className="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600"
                        placeholder="Se completa al elegir la factura"
                    />
                    <p className="text-xs text-gray-400 mt-1">
                        El pago cubre el total de la factura (no se permiten pagos parciales).
                    </p>
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Método de pago</label>
                    <select
                        value={metodoPago}
                        onChange={e => setMetodoPago(e.target.value)}
                        className="w-full border rounded-lg px-3 py-2"
                    >
                        <option value="Efectivo">Efectivo</option>
                        <option value="QR">QR</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                    </select>
                </div>

                {metodoPago === 'QR' && (
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Referencia del QR</label>
                        <input
                            type="text"
                            value={referenciaQr}
                            onChange={e => setReferenciaQr(e.target.value)}
                            className="w-full border rounded-lg px-3 py-2"
                            placeholder="N° de operación / comprobante"
                        />
                    </div>
                )}

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Fecha de pago</label>
                    <input
                        type="date"
                        value={fechaPago}
                        onChange={e => setFechaPago(e.target.value)}
                        className="w-full border rounded-lg px-3 py-2"
                    />
                </div>

                <div className="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        onClick={() => navigate('/pagos')}
                        className="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-50"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        disabled={enviando || !facturaId}
                        className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {enviando ? 'Guardando...' : 'Registrar pago'}
                    </button>
                </div>

            </form>
        </Layout>
    );
};

export default NuevoPago;