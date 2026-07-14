import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';
import { useNavigate } from 'react-router-dom';

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

    const navigate = useNavigate();

    useEffect(() => {
        api.get('/facturas')
            .then(res => {
                console.log('Respuesta cruda de /facturas:', res.data);
                const payload = res.data.data;
                const lista = Array.isArray(payload)
                    ? payload
                    : Array.isArray(payload?.data)
                        ? payload.data
                        : [];
                setFacturas(lista);
            })
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

    const handleAnular = async (id: number, numero: string) => {
        if (!confirm(`¿Anular la factura ${numero}?`)) return;
        try {
            await api.put(`/facturas/${id}`, { estado: 'Anulada' });
            setFacturas(prev => prev.map(f => f.id === id ? { ...f, estado: 'Anulada' } : f));
        } catch (err) {
            console.error(err);
            alert('No se pudo anular la factura.');
        }
    };

    return (
        <Layout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Facturas</h1>
                <button
                    onClick={() => navigate('/facturas/nueva')}
                    className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
                >
                    + Nueva Factura
                </button>
            </div>

            <div className="bg-white rounded-lg shadow overflow-hidden">
                {loading ? (
                    <div className="p-8 text-center text-gray-500">Cargando...</div>
                ) : facturas.length === 0 ? (
                    <div className="p-8 text-center text-gray-500">No hay facturas registradas.</div>
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
                                        <button
                                            onClick={() => navigate(`/facturas/${factura.id}`)}
                                            className="text-blue-600 hover:text-blue-800 mr-3"
                                        >
                                            Ver
                                        </button>
                                        <button
                                            onClick={() => handleAnular(factura.id, factura.numero)}
                                            className="text-red-600 hover:text-red-800"
                                        >
                                            Anular
                                        </button>
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