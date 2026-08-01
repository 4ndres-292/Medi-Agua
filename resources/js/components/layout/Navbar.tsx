import React, { useState, useRef, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

const Navbar: React.FC = () => {
    const [menuOpen, setMenuOpen] = useState(false);
    const [adminOpen, setAdminOpen] = useState(false);
    const [reportesOpen, setReportesOpen] = useState(false);
    const [userOpen, setUserOpen] = useState(false);

    const navigate = useNavigate();

    const handleLogout = async () => {
        await logout();
        navigate('/login');
    };

    const closeAll = () => {
        setAdminOpen(false);
        setReportesOpen(false);
        setUserOpen(false);
        setMenuOpen(false);
    };

    return (
        <header ref={navRef} className="w-full bg-sky-600 text-white shadow-md relative z-50">

            {/* TOP BAR */}
            <div className="flex items-center justify-between px-4 md:px-8 py-3">

                <Link to="/dashboard" className="text-lg font-bold tracking-wide" onClick={closeAll}>
                    🚰 Medi-Agua
                </Link>

                {/* USER DESKTOP */}
                <div className="hidden md:flex items-center gap-6">

                    <div className="relative group cursor-pointer">
                        <span className="hover:text-sky-100 transition">
                            Andrés ▼
                        </span>

                        {userOpen && (
                            <div className="absolute right-0 mt-2 w-48 bg-white text-slate-800 rounded shadow-lg z-50">
                                <Link to="/perfil" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>
                                    Mi perfil
                                </Link>
                                <Link to="/configuracion" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>
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
                        )}
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

                <Link to="/dashboard" className="hover:text-sky-200" onClick={closeAll}>
                    Dashboard
                </Link>

                <div className="relative">
                    <button
                        onClick={() => { setAdminOpen(!adminOpen); setReportesOpen(false); }}
                        className="hover:text-sky-200"
                    >
                        Administración ▼
                    </button>

                    {adminOpen && (
                        <div className="absolute left-0 mt-2 w-56 bg-white text-slate-800 rounded shadow-lg z-50">
                            <Link to="/users" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Usuarios</Link>
                            <Link to="/roles" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Roles</Link>
                            <Link to="/socios" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Socios</Link>
                            <Link to="/medidores" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Medidores</Link>
                            <Link to="/lecturas" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Lecturas</Link>
                            <Link to="/tarifas" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Tarifas</Link>
                            <Link to="/facturas" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Facturas</Link>
                            <Link to="/pagos" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Pagos</Link>
                            <Link to="/notificaciones" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Notificaciones</Link>
                        </div>
                    )}
                </div>

                <div className="relative">
                    <button
                        onClick={() => { setReportesOpen(!reportesOpen); setAdminOpen(false); }}
                        className="hover:text-sky-200"
                    >
                        Reportes ▼
                    </button>

                    {reportesOpen && (
                        <div className="absolute left-0 mt-2 w-56 bg-white text-slate-800 rounded shadow-lg z-50">
                            <Link to="/reportes/ingresos" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Ingresos</Link>
                            <Link to="/reportes/deudores" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Deudores</Link>
                            <Link to="/reportes/consumo" className="block px-4 py-2 hover:bg-sky-50" onClick={closeAll}>Consumo</Link>
                        </div>
                    )}
                </div>

            </nav>

            {/* MOBILE */}
            {menuOpen && (
                <div className="md:hidden bg-sky-700 px-4 py-3 space-y-2">
                    <Link to="/dashboard" className="block" onClick={closeAll}>Dashboard</Link>
                    <Link to="/users" className="block" onClick={closeAll}>Usuarios</Link>
                    <Link to="/roles" className="block" onClick={closeAll}>Roles</Link>
                    <Link to="/socios" className="block" onClick={closeAll}>Socios</Link>
                    <Link to="/medidores" className="block" onClick={closeAll}>Medidores</Link>
                    <Link to="/lecturas" className="block" onClick={closeAll}>Lecturas</Link>
                    <Link to="/tarifas" className="block" onClick={closeAll}>Tarifas</Link>
                    <Link to="/facturas" className="block" onClick={closeAll}>Facturas</Link>
                    <Link to="/pagos" className="block" onClick={closeAll}>Pagos</Link>
                    <Link to="/notificaciones" className="block" onClick={closeAll}>Notificaciones</Link>
                    <hr className="border-sky-500" />
                    <Link to="/reportes/ingresos" className="block" onClick={closeAll}>Ingresos</Link>
                    <Link to="/reportes/deudores" className="block" onClick={closeAll}>Deudores</Link>
                    <Link to="/reportes/consumo" className="block" onClick={closeAll}>Consumo</Link>
                    <hr className="border-sky-500" />
                    <Link to="/perfil" className="block" onClick={closeAll}>Mi Perfil</Link>
                    <Link to="/configuracion" className="block" onClick={closeAll}>Configuración</Link>
                    <hr className="border-sky-500" />
                    <button onClick={handleLogout} className="block text-red-200">
                        Cerrar sesión
                    </button>
                </div>
            )}

        </header>
    );
};

export default Navbar;