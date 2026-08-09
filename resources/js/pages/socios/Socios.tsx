import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import Layout from '../../components/layout/Layout';

interface Socio {
    id: number;
    nombres: string;
    apellidos: string;
    ci: string;
    telefono: string;
    direccion: string;
    estado: string;
}

interface SocioFormData {
    nombres: string;
    apellidos: string;
    ci: string;
    telefono: string;
    direccion: string;
    estado: string;
}

const initialFormData: SocioFormData = {
    nombres: '',
    apellidos: '',
    ci: '',
    telefono: '',
    direccion: '',
    estado: 'activo',
};

const Socios: React.FC = () => {
    const [socios, setSocios] = useState<Socio[]>([]);
    const [loading, setLoading] = useState(true);

    const [showModal, setShowModal] = useState(false);
    const [formData, setFormData] = useState<SocioFormData>(initialFormData);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        fetchSocios();
    }, []);

    const fetchSocios = () => {
        setLoading(true);
        api.get('/socios')
            .then(res => {
                // El index() de Laravel devuelve ->paginate(), que anida
                // el array real en data.data.data (objeto de paginación
                // dentro del wrapper { success, message, data }).
                const payload = res.data?.data;
                const lista = Array.isArray(payload)
                    ? payload
                    : (payload?.data ?? []);
                setSocios(Array.isArray(lista) ? lista : []);
            })
            .catch(err => console.error(err))
            .finally(() => setLoading(false));
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
            await api.post('/socios', formData);
            closeModal();
            fetchSocios();
        } catch (err: any) {
            // Laravel devuelve 422 con { errors: { campo: [mensajes] } }
            const backendErrors = err.response?.data?.errors;
            if (backendErrors) {
                const mapped: Record<string, string> = {};
                Object.keys(backendErrors).forEach(key => {
                    mapped[key] = backendErrors[key][0];
                });
                setErrors(mapped);
            } else {
                console.error(err);
                setErrors({ general: 'No se pudo guardar el socio. Intenta de nuevo.' });
            }
        } finally {
            setSaving(false);
        }
    };

    return (
        <Layout>

            {/* =====================================================
                SOCIOS
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
                            Socios
                        </h1>
                        <button
                            onClick={openModal}
                            className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
                        >
                            + Nuevo Socio
                        </button>
                    </div>

                    <div className="bg-white/95 rounded-lg shadow-[0_8px_30px_rgba(15,75,110,0.08)] overflow-hidden">
                        {loading ? (
                            <div className="p-8 text-center text-gray-500">Cargando...</div>
                        ) : socios.length === 0 ? (
                            <div className="p-8 text-center text-gray-500">
                                Todavía no hay socios registrados.
                            </div>
                        ) : (
                            <table className="w-full text-left">
                                <thead className="bg-gray-50 border-b">
                                    <tr>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">ID</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Nombre</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">CI</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Teléfono</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Estado</th>
                                        <th className="px-6 py-3 text-sm font-medium text-gray-500">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200">
                                    {socios.map(socio => (
                                        <tr key={socio.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 text-sm text-gray-900">{socio.id}</td>
                                            <td className="px-6 py-4 text-sm text-gray-900">{socio.nombres} {socio.apellidos}</td>
                                            <td className="px-6 py-4 text-sm text-gray-500">{socio.ci}</td>
                                            <td className="px-6 py-4 text-sm text-gray-500">{socio.telefono}</td>
                                            <td className="px-6 py-4 text-sm">
                                                <span className={`px-2 py-1 rounded-full text-xs ${socio.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                                    {socio.estado}
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
                MODAL — NUEVO SOCIO
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
                            Nuevo Socio
                        </h2>

                        {errors.general && (
                            <div className="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                                {errors.general}
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-4">

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Nombres
                                </label>
                                <input
                                    type="text"
                                    name="nombres"
                                    value={formData.nombres}
                                    onChange={handleChange}
                                    placeholder="Ej: Juan"
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                {errors.nombres && (
                                    <p className="text-xs text-red-600 mt-1">{errors.nombres}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Apellidos
                                </label>
                                <input
                                    type="text"
                                    name="apellidos"
                                    value={formData.apellidos}
                                    onChange={handleChange}
                                    placeholder="Ej: Pérez"
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                {errors.apellidos && (
                                    <p className="text-xs text-red-600 mt-1">{errors.apellidos}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    CI
                                </label>
                                <input
                                    type="text"
                                    name="ci"
                                    value={formData.ci}
                                    onChange={handleChange}
                                    placeholder="Solo dígitos"
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                {errors.ci && (
                                    <p className="text-xs text-red-600 mt-1">{errors.ci}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Teléfono (opcional)
                                </label>
                                <input
                                    type="text"
                                    name="telefono"
                                    value={formData.telefono}
                                    onChange={handleChange}
                                    placeholder="Solo dígitos"
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                {errors.telefono && (
                                    <p className="text-xs text-red-600 mt-1">{errors.telefono}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Dirección (opcional)
                                </label>
                                <input
                                    type="text"
                                    name="direccion"
                                    value={formData.direccion}
                                    onChange={handleChange}
                                    placeholder="Ej: Av. Principal #123"
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                {errors.direccion && (
                                    <p className="text-xs text-red-600 mt-1">{errors.direccion}</p>
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

export default Socios;