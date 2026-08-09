import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface Medidor {
    id: number;
    codigo: string;
    socio_id: number;
    socio?: {
        id: number;
        nombres: string;
        apellidos: string;
    };
    estado: string;
    ubicacion: string;
}

interface Socio {
    id: number;
    nombres: string;
    apellidos: string;
}

interface MedidorFormData {
    codigo: string;
    socio_id: string;
    ubicacion: string;
    estado: string;
}

const initialFormData: MedidorFormData = {
    codigo: '',
    socio_id: '',
    ubicacion: '',
    estado: 'activo',
};

const Medidores: React.FC = () => {
    const [medidores, setMedidores] = useState<Medidor[]>([]);
    const [socios, setSocios] = useState<Socio[]>([]);
    const [loading, setLoading] = useState(true);

    const [showModal, setShowModal] = useState(false);
    const [formData, setFormData] = useState<MedidorFormData>(initialFormData);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        fetchMedidores();
        fetchSocios();
    }, []);

    const fetchMedidores = () => {
        setLoading(true);
        api.get('/medidores')
            .then(res => {
                // El index() de Laravel usa ->paginate(), así que el array
                // real puede venir anidado en data.data.data.
                const payload = res.data?.data;
                const lista = Array.isArray(payload)
                    ? payload
                    : (payload?.data ?? []);
                setMedidores(Array.isArray(lista) ? lista : []);
            })
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
    };

    const fetchSocios = () => {
        api.get('/socios')
            .then(res => {
                const payload = res.data?.data;
                const lista = Array.isArray(payload)
                    ? payload
                    : (payload?.data ?? []);
                setSocios(Array.isArray(lista) ? lista : []);
            })
            .catch(err => console.error(err));
    };

    const openModal = () => {
        setFormData(initialFormData);
        setErrors({});
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setErrors({});
    };

    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>
    ) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);
        setErrors({});

        try {
            await api.post('/medidores', {
                ...formData,
                socio_id: Number(formData.socio_id),
            });
            closeModal();
            fetchMedidores();
        } catch (err: any) {
            const backendErrors = err.response?.data?.errors;
            if (backendErrors) {
                const mapped: Record<string, string> = {};
                Object.keys(backendErrors).forEach(key => {
                    mapped[key] = backendErrors[key][0];
                });
                setErrors(mapped);
            } else {
                console.error(err);
                setErrors({ general: 'No se pudo guardar el medidor. Intenta de nuevo.' });
            }
        } finally {
            setSaving(false);
        }
    };

    return (
        <Layout>

            {/* =====================================================
                MEDIDORES
            ====================================================== */}

            <section className="
                relative
                overflow-hidden
                min-h-screen
            ">

                {/* =================================================
                    FONDO CON IMAGEN REAL (detrás de todo el contenido)
                ================================================== */}
                <div
                    className="
                        absolute
                        inset-0
                        bg-cover
                        bg-center
                        bg-no-repeat
                        z-0
                    "
                    style={{
                        backgroundImage: "url('/images/fondo-dashboard.jpg')",
                    }}
                />

                {/* OVERLAY MUY SUAVE — deja ver la foto, solo da un poco
                    de contraste arriba/abajo para que el texto se lea */}
                <div className="
                    absolute
                    inset-0
                    bg-gradient-to-b
                    from-white/20
                    via-white/10
                    to-white/20
                    z-0
                " />

                {/* CONTENIDO */}
                <div className="
                    relative
                    z-10
                    max-w-[1400px]
                    mx-auto
                    px-8
                    lg:px-14
                    pt-14
                    pb-16
                ">

                    <div className="flex items-center justify-between mb-6">
                        <h1 className="text-3xl font-bold text-gray-800 drop-shadow-sm">
                            Medidores
                        </h1>
                        <button
                            onClick={openModal}
                            className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
                        >
                            + Nuevo Medidor
                        </button>
                    </div>

                    <div className="bg-white/95 rounded-lg shadow-[0_8px_30px_rgba(15,75,110,0.08)] overflow-hidden">
                        {loading ? (
                            <div className="p-8 text-center text-gray-500">Cargando...</div>
                        ) : medidores.length === 0 ? (
                            <div className="p-8 text-center text-gray-500">
                                Todavía no hay medidores registrados.
                            </div>
                        ) : (
                            <table className="w-full text-left">
                                <thead className="bg-gray-50 border-b">
                                    <tr>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">ID</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Código</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Socio</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Ubicación</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Estado</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200">
                                    {medidores.map(medidor => (
                                        <tr key={medidor.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 text-sm text-gray-900">{medidor.id}</td>
                                            <td className="px-6 py-4 text-sm text-gray-900">{medidor.codigo}</td>
                                            <td className="px-6 py-4 text-sm text-gray-500">
                                                {medidor.socio
                                                    ? `${medidor.socio.nombres} ${medidor.socio.apellidos}`
                                                    : medidor.socio_id}
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-500">{medidor.ubicacion}</td>
                                            <td className="px-6 py-4 text-sm">
                                                <span className={`px-2 py-1 rounded-full text-xs ${medidor.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                                    {medidor.estado}
                                                </span>
                                            </td>
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

                </div>

            </section>

            {/* =====================================================
                MODAL — NUEVO MEDIDOR
            ====================================================== */}
            {showModal && (
                <div className="
                    fixed
                    inset-0
                    z-50
                    flex
                    items-center
                    justify-center
                    bg-black/40
                    p-4
                ">
                    <div className="
                        bg-white
                        rounded-xl
                        shadow-xl
                        w-full
                        max-w-md
                        p-6
                    ">
                        <h2 className="text-xl font-bold text-gray-800 mb-5">
                            Nuevo Medidor
                        </h2>

                        {errors.general && (
                            <div className="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                                {errors.general}
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-4">

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Código
                                </label>
                                <input
                                    type="text"
                                    name="codigo"
                                    value={formData.codigo}
                                    onChange={handleChange}
                                    placeholder="Ej: 000020"
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                {errors.codigo && (
                                    <p className="text-xs text-red-600 mt-1">{errors.codigo}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Socio
                                </label>
                                <select
                                    name="socio_id"
                                    value={formData.socio_id}
                                    onChange={handleChange}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Selecciona un socio...</option>
                                    {socios.map(socio => (
                                        <option key={socio.id} value={socio.id}>
                                            {socio.nombres} {socio.apellidos}
                                        </option>
                                    ))}
                                </select>
                                {errors.socio_id && (
                                    <p className="text-xs text-red-600 mt-1">{errors.socio_id}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Ubicación
                                </label>
                                <input
                                    type="text"
                                    name="ubicacion"
                                    value={formData.ubicacion}
                                    onChange={handleChange}
                                    placeholder="Ej: Av. Principal #123"
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                {errors.ubicacion && (
                                    <p className="text-xs text-red-600 mt-1">{errors.ubicacion}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Estado
                                </label>
                                <select
                                    name="estado"
                                    value={formData.estado}
                                    onChange={handleChange}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>

                            <div className="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={closeModal}
                                    disabled={saving}
                                    className="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50 transition disabled:opacity-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    disabled={saving}
                                    className="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition disabled:opacity-50"
                                >
                                    {saving ? 'Guardando...' : 'Guardar'}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            )}

        </Layout>
    );
};

export default Medidores;