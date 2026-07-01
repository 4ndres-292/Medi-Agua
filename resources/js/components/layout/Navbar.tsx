import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import api from '../../services/api';

const Navbar: React.FC = () => {
    const [menuOpen, setMenuOpen] = useState(false);
    const navigate = useNavigate();

    const handleLogout = async () => {
        try {
            await api.post('/logout');
        } catch (error) {
            console.log('Logout error:', error);
        } finally {
            localStorage.removeItem('token');
            navigate('/login');
        }
    };

    return (
        <header className="w-full bg-sky-600 text-white shadow-md">

            {/* TOP BAR */}
            <div className="flex items-center justify-between px-4 md:px-8 py-3">

                <Link to="/dashboard" className="text-lg font-bold tracking-wide">
                    🚰 Medi-Agua
                </Link>

                {/* USER DESKTOP */}
                <div className="hidden md:flex items-center gap-6">

                    <div className="relative group cursor-pointer">
                        <span className="hover:text-sky-100 transition">
                            Andrés ▼
                        </span>

                        <div className="absolute right-0 mt-2 w-48 bg-white text-slate-800 rounded shadow-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-200">

                            <Link to="/perfil" className="block px-4 py-2 hover:bg-sky-50">
                                Mi perfil
                            </Link>

                            <Link to="/configuracion" className="block px-4 py-2 hover:bg-sky-50">
                                Configuración
                            </Link>

                            <hr />

                            <button
                                onClick={handleLogout}
                                className="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600"
                            >
                                Cerrar sesión
                            </button>

                        </div>
                    </div>
                </div>

                {/* MOBILE MENU */}
                <button
                    className="md:hidden text-2xl"
                    onClick={() => setMenuOpen(!menuOpen)}
                >
                    ☰
                </button>
            </div>

            {/* MENU PRINCIPAL */}
            <nav className="hidden md:flex bg-sky-700 px-8 py-2 gap-6 text-sm">

                <Link to="/dashboard" className="hover:text-sky-200">
                    Dashboard
                </Link>

                <div className="relative group cursor-pointer">
                    <span className="hover:text-sky-200">
                        Administración ▼
                    </span>

                    <div className="absolute left-0 mt-2 w-56 bg-white text-slate-800 rounded shadow-lg opacity-0 invisible group-hover:visible group-hover:opacity-100 transition">

                        <Link to="/users" className="block px-4 py-2 hover:bg-sky-50">Usuarios</Link>
                        <Link to="/roles" className="block px-4 py-2 hover:bg-sky-50">Roles</Link>
                        <Link to="/socios" className="block px-4 py-2 hover:bg-sky-50">Socios</Link>
                        <Link to="/medidores" className="block px-4 py-2 hover:bg-sky-50">Medidores</Link>
                        <Link to="/lecturas" className="block px-4 py-2 hover:bg-sky-50">Lecturas</Link>
                        <Link to="/tarifas" className="block px-4 py-2 hover:bg-sky-50">Tarifas</Link>
                        <Link to="/facturas" className="block px-4 py-2 hover:bg-sky-50">Facturas</Link>
                        <Link to="/pagos" className="block px-4 py-2 hover:bg-sky-50">Pagos</Link>
                        <Link to="/notificaciones" className="block px-4 py-2 hover:bg-sky-50">Notificaciones</Link>

                    </div>
                </div>

                <div className="relative group cursor-pointer">
                    <span className="hover:text-sky-200">
                        Reportes ▼
                    </span>

                    <div className="absolute left-0 mt-2 w-56 bg-white text-slate-800 rounded shadow-lg opacity-0 invisible group-hover:visible group-hover:opacity-100 transition">

                        <Link to="/reportes/ingresos" className="block px-4 py-2 hover:bg-sky-50">Ingresos</Link>
                        <Link to="/reportes/deudores" className="block px-4 py-2 hover:bg-sky-50">Deudores</Link>
                        <Link to="/reportes/consumo" className="block px-4 py-2 hover:bg-sky-50">Consumo</Link>

                    </div>
                </div>

            </nav>

            {/* MOBILE */}
            {menuOpen && (
                <div className="md:hidden bg-sky-700 px-4 py-3 space-y-2">

                    <Link to="/dashboard" className="block">Dashboard</Link>
                    <Link to="/users" className="block">Usuarios</Link>
                    <Link to="/roles" className="block">Roles</Link>
                    <Link to="/socios" className="block">Socios</Link>
                    <Link to="/medidores" className="block">Medidores</Link>
                    <Link to="/lecturas" className="block">Lecturas</Link>
                    <Link to="/tarifas" className="block">Tarifas</Link>
                    <Link to="/facturas" className="block">Facturas</Link>
                    <Link to="/pagos" className="block">Pagos</Link>
                    <Link to="/notificaciones" className="block">Notificaciones</Link>

                    <hr className="border-sky-500" />

                    <Link to="/reportes/ingresos" className="block">Ingresos</Link>
                    <Link to="/reportes/deudores" className="block">Deudores</Link>
                    <Link to="/reportes/consumo" className="block">Consumo</Link>

                    <hr className="border-sky-500" />

                    <Link to="/perfil" className="block">Mi Perfil</Link>
                    <Link to="/configuracion" className="block">Configuración</Link>

                    <hr className="border-sky-500" />

                    <button
                        onClick={handleLogout}
                        className="block text-red-200"
                    >
                        Cerrar sesión
                    </button>

                </div>
            )}

        </header>
    );
};

export default Navbar;
