import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

const Login: React.FC = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [mostrarPassword, setMostrarPassword] = useState(false);
    const [recordarme, setRecordarme] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const navigate = useNavigate();
    const { login } = useAuth();

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const response = await api.post('/login', {
                email,
                password,
            });

            console.log('LOGIN OK:', response.data);

            localStorage.setItem('token', response.data.data.token);

            // 👉 redirigir a dashboard
            navigate('/dashboard');
        } catch (err: any) {
            console.error(err);
            setError(
                err?.response?.data?.message ||
                'Error al iniciar sesión'
            );
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen relative overflow-hidden bg-sky-100">

            {/* Foto de fondo — reemplaza la ruta por la tuya en public/images/ */}
            <img
                src="/images/agua-splash.jpg"
                alt=""
                className="absolute inset-0 w-full h-full object-cover"
            />

            {/* Degradado encima de la foto para que el texto se lea bien */}
            <div className="absolute inset-0 bg-gradient-to-b from-white/10 via-sky-900/10 to-sky-950/60" />
            <div className="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-white via-white/60 to-transparent md:hidden" />

            {/* Logo + nombre, arriba a la izquierda */}
            <div className="absolute top-6 left-6 md:top-10 md:left-10 z-20 flex items-center gap-3">
                <div className="w-11 h-11 rounded-full bg-white/90 flex items-center justify-center text-xl shadow">
                    💧
                </div>
                <div>
                    <h1 className="text-white font-extrabold text-lg leading-tight drop-shadow">
                        OTB AGUA
                    </h1>
                    <p className="text-sky-100 text-xs drop-shadow">Sistema de Gestión</p>
                </div>
            </div>

            {/* Texto descriptivo, visible solo en escritorio, abajo a la izquierda */}
            <div className="hidden md:block absolute bottom-10 left-10 z-20 max-w-xs">
                <p className="text-white text-sm leading-relaxed drop-shadow">
                    Gestión eficiente y transparente para el servicio
                    de agua potable de nuestra comunidad.
                </p>
            </div>

            {/* Tarjeta de login:
                - En celular: pegada abajo, ancho completo, esquinas redondeadas arriba (tipo "hoja").
                - En escritorio: centrada horizontal y verticalmente (ajustado por pedido del usuario,
                  antes estaba pegada al borde derecho con md:right-16). */}
            <div className="absolute inset-x-0 bottom-0 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/3 md:-translate-y-1/2 z-20 w-full md:w-[380px] px-4 pb-4 md:px-0 md:pb-0">
                <div className="bg-white rounded-t-3xl md:rounded-2xl shadow-xl p-6 md:p-8">

                    <h2 className="text-xl md:text-2xl font-bold text-gray-800 mb-1">Bienvenido</h2>
                    <p className="text-gray-500 text-sm mb-5">Inicia sesión para continuar</p>

                    {error && (
                        <div className="bg-red-100 text-red-600 p-3 rounded-lg mb-4 text-sm">
                            {error}
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-4">

                        <div>
                            <label className="block mb-1 text-sm font-medium text-gray-700">
                                Correo electrónico
                            </label>
                            <div className="relative">
                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                    👤
                                </span>
                                <input
                                    type="email"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    placeholder="Ingresa tu correo electrónico"
                                    className="w-full border rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block mb-1 text-sm font-medium text-gray-700">
                                Contraseña
                            </label>
                            <div className="relative">
                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                    🔒
                                </span>
                                <input
                                    type={mostrarPassword ? 'text' : 'password'}
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    placeholder="Ingresa tu contraseña"
                                    className="w-full border rounded-lg pl-10 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                                    required
                                />
                                <button
                                    type="button"
                                    onClick={() => setMostrarPassword(!mostrarPassword)}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                >
                                    {mostrarPassword ? '🙈' : '👁️'}
                                </button>
                            </div>
                        </div>

                        <div className="flex items-center justify-between text-xs">
                            <label className="flex items-center gap-2 text-gray-600">
                                <input
                                    type="checkbox"
                                    checked={recordarme}
                                    onChange={(e) => setRecordarme(e.target.checked)}
                                    className="rounded border-gray-300"
                                />
                                Recordarme
                            </label>
                            <span className="text-sky-600 cursor-not-allowed opacity-70" title="Próximamente">
                                ¿Olvidaste tu contraseña?
                            </span>
                        </div>

                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full bg-sky-600 hover:bg-sky-700 text-white py-2.5 rounded-lg transition disabled:bg-gray-400 font-medium text-sm"
                        >
                            {loading ? 'Ingresando...' : 'Iniciar sesión'}
                        </button>
                    </form>

                    <div className="flex items-center gap-3 my-4">
                        <div className="flex-1 h-px bg-gray-200" />
                        <span className="text-xs text-gray-400">o</span>
                        <div className="flex-1 h-px bg-gray-200" />
                    </div>

                    <button
                        type="button"
                        disabled
                        className="w-full border rounded-lg py-2.5 text-gray-400 cursor-not-allowed flex items-center justify-center gap-2 text-sm"
                        title="Próximamente"
                    >
                        <span>🔵</span> Continuar con Google
                    </button>

                    <p className="text-center text-xs text-gray-400 mt-4">
                        🛡️ Tu información está protegida
                    </p>
                </div>
            </div>
        </div>
    );
};

export default Login;