import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

const Navbar: React.FC = () => {
    const [menuOpen, setMenuOpen] = useState(false);
    const [adminOpen, setAdminOpen] = useState(false);
    const [reportesOpen, setReportesOpen] = useState(false);
    const [userOpen, setUserOpen] = useState(false);

    const navigate = useNavigate();
    const { logout, user } = useAuth();

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

    const toggleAdmin = () => {
        setAdminOpen(!adminOpen);
        setReportesOpen(false);
        setUserOpen(false);
    };

    const toggleReportes = () => {
        setReportesOpen(!reportesOpen);
        setAdminOpen(false);
        setUserOpen(false);
    };

    const toggleUser = () => {
        setUserOpen(!userOpen);
        setAdminOpen(false);
        setReportesOpen(false);
    };

    return (
        <header className="relative z-50 w-full">

            {/* =====================================================
                BARRA SUPERIOR
                COLOR PRINCIPAL OTB AGUA
            ====================================================== */}

            <div
                className="
                    h-[68px]
                    w-full
                    bg-[#003B70]
                    flex
                    items-center
                "
            >

                {/* =================================================
                    BOTÓN HAMBURGUESA
                ================================================== */}

                <button
                    type="button"
                    onClick={() => setMenuOpen(!menuOpen)}
                    className="
                        h-full
                        w-[58px]
                        flex
                        items-center
                        justify-center
                        text-white
                        bg-[#003B70]
                        hover:bg-[#064C82]
                        transition-colors
                    "
                    aria-label="Abrir menú"
                >
                    <svg
                        className="w-[21px] h-[21px]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>


                {/* =================================================
                    LOGO OTB AGUA
                ================================================== */}

                <Link
                    to="/dashboard"
                    onClick={closeAll}
                    className="
                        h-full
                        w-[255px]
                        px-5
                        flex
                        items-center
                        bg-[#003B70]
                        flex-shrink-0
                    "
                >

                    {/* GOTA */}

                    <div
                        className="
                            w-[43px]
                            h-[43px]
                            rounded-full
                            bg-white
                            flex
                            items-center
                            justify-center
                            mr-3
                        "
                    >
                        <svg
                            className="w-[26px] h-[26px] text-[#0788CF]"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path
                                d="
                                    M12 2
                                    C12 2 5 9.2 5 14.2
                                    C5 18.3 8.1 21 12 21
                                    C15.9 21 19 18.3 19 14.2
                                    C19 9.2 12 2 12 2Z
                                "
                            />
                        </svg>
                    </div>


                    {/* NOMBRE */}

                    <div className="leading-none">

                        <div
                            className="
                                text-white
                                font-bold
                                text-[19px]
                                tracking-wide
                            "
                        >
                            OTB AGUA
                        </div>

                        <div
                            className="
                                text-white
                                text-[10px]
                                mt-[5px]
                                opacity-95
                            "
                        >
                            Control de Gestión
                        </div>

                    </div>

                </Link>


                {/* =================================================
                    PARTE DERECHA
                ================================================== */}

                <div
                    className="
                        flex-1
                        h-full
                        flex
                        items-center
                        justify-end
                    "
                >

                    {/* =================================================
                        REPORTES
                    ================================================== */}

                    <div className="relative h-full">

                        <button
                            type="button"
                            onClick={toggleReportes}
                            className="
                                h-full
                                px-8
                                text-white
                                text-[13px]
                                font-medium
                                flex
                                items-center
                                gap-3
                                bg-[#003B70]
                                hover:bg-[#064C82]
                                transition-colors
                            "
                        >
                            Reportes

                            <svg
                                className={`
                                    w-[14px]
                                    h-[14px]
                                    transition-transform
                                    ${
                                        reportesOpen
                                            ? 'rotate-180'
                                            : ''
                                    }
                                `}
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d="M6 9l6 6 6-6"
                                />
                            </svg>
                        </button>


                        {/* MENÚ REPORTES */}

                        {reportesOpen && (
                            <div
                                className="
                                    absolute
                                    top-[68px]
                                    left-1/2
                                    -translate-x-1/2
                                    w-[145px]
                                    bg-white
                                    shadow-[0_5px_18px_rgba(0,0,0,0.18)]
                                    border
                                    border-[#D9EEF8]
                                    overflow-hidden
                                "
                            >

                                <ReportLink
                                    to="/reportes/ingresos"
                                    icon="▥"
                                    text="Ingresos"
                                    onClick={closeAll}
                                />

                                <ReportLink
                                    to="/reportes/deudores"
                                    icon="▤"
                                    text="Deudores"
                                    onClick={closeAll}
                                />

                                <ReportLink
                                    to="/reportes/consumo"
                                    icon="💧"
                                    text="Consumo"
                                    onClick={closeAll}
                                />

                            </div>
                        )}

                    </div>


                    {/* =================================================
                        ADMINISTRACIÓN
                    ================================================== */}

                    <div className="relative h-full">

                        <button
                            type="button"
                            onClick={toggleAdmin}
                            className="
                                h-full
                                px-8
                                text-white
                                text-[13px]
                                font-medium
                                flex
                                items-center
                                gap-3
                                bg-[#003B70]
                                hover:bg-[#064C82]
                                transition-colors
                            "
                        >
                            Administración

                            <svg
                                className={`
                                    w-[14px]
                                    h-[14px]
                                    transition-transform
                                    ${
                                        adminOpen
                                            ? 'rotate-180'
                                            : ''
                                    }
                                `}
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d="M6 9l6 6 6-6"
                                />
                            </svg>
                        </button>


                        {/* MENÚ ADMINISTRACIÓN */}

                        {adminOpen && (
                            <div
                                className="
                                    absolute
                                    top-[68px]
                                    right-0
                                    w-[158px]
                                    bg-white
                                    shadow-[0_5px_18px_rgba(0,0,0,0.18)]
                                    border
                                    border-[#D9EEF8]
                                    overflow-hidden
                                "
                            >

                                <AdminLink
                                    to="/users"
                                    icon="♙"
                                    text="Usuarios"
                                    onClick={closeAll}
                                />

                                <AdminLink
                                    to="/roles"
                                    icon="♙"
                                    text="Roles"
                                    onClick={closeAll}
                                />

                                <AdminLink
                                    to="/socios"
                                    icon="♧"
                                    text="Socios"
                                    onClick={closeAll}
                                />

                                <AdminLink
                                    to="/medidores"
                                    icon="◉"
                                    text="Medidores"
                                    onClick={closeAll}
                                />

                                <AdminLink
                                    to="/lecturas"
                                    icon="▤"
                                    text="Lecturas"
                                    onClick={closeAll}
                                />

                                <AdminLink
                                    to="/tarifas"
                                    icon="▧"
                                    text="Tarifas"
                                    onClick={closeAll}
                                />

                                <AdminLink
                                    to="/facturas"
                                    icon="▣"
                                    text="Facturas"
                                    onClick={closeAll}
                                />

                                <AdminLink
                                    to="/pagos"
                                    icon="▣"
                                    text="Pagos"
                                    onClick={closeAll}
                                />

                                <AdminLink
                                    to="/notificaciones"
                                    icon="♧"
                                    text="Notificaciones"
                                    onClick={closeAll}
                                />

                            </div>
                        )}

                    </div>


                    {/* =================================================
                        USUARIO
                    ================================================== */}

                    <div
                        className="
                            relative
                            flex
                            items-center
                            gap-3
                            px-4
                        "
                    >

                        {/* ICONO DE USUARIO */}

                        <button
                            type="button"
                            onClick={toggleUser}
                            className="
                                w-[32px]
                                h-[32px]
                                rounded-full
                                border
                                border-white
                                flex
                                items-center
                                justify-center
                                text-white
                                hover:bg-[#064C82]
                                transition-colors
                            "
                            aria-label="Perfil"
                        >
                            <svg
                                className="w-[20px] h-[20px]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    cx="12"
                                    cy="8"
                                    r="3.5"
                                    strokeWidth="1.4"
                                />

                                <path
                                    d="M5 21c0-4 3-6 7-6s7 2 7 6"
                                    strokeWidth="1.4"
                                />
                            </svg>
                        </button>


                        {/* NOMBRE USUARIO */}

                        <button
                            type="button"
                            onClick={toggleUser}
                            className="
                                h-[38px]
                                min-w-[94px]
                                px-4
                                rounded-md
                                bg-[#087FC3]
                                hover:bg-[#064C82]
                                text-white
                                flex
                                items-center
                                justify-center
                                gap-2
                                text-[12px]
                                font-medium
                                transition-colors
                            "
                        >

                            {user?.username || 'bcaliza'}

                            <span className="text-[9px]">
                                ▼
                            </span>

                        </button>


                        {/* MENÚ USUARIO */}

                        {userOpen && (
                            <div
                                className="
                                    absolute
                                    right-4
                                    top-[58px]
                                    w-[180px]
                                    bg-white
                                    shadow-[0_5px_18px_rgba(0,0,0,0.18)]
                                    border
                                    border-[#D9EEF8]
                                    overflow-hidden
                                "
                            >

                                <Link
                                    to="/perfil"
                                    onClick={closeAll}
                                    className="
                                        block
                                        px-4
                                        py-3
                                        text-sm
                                        text-[#526779]
                                        hover:bg-[#EFF9FE]
                                        hover:text-[#0788CF]
                                    "
                                >
                                    Mi perfil
                                </Link>

                                <Link
                                    to="/configuracion"
                                    onClick={closeAll}
                                    className="
                                        block
                                        px-4
                                        py-3
                                        text-sm
                                        text-[#526779]
                                        hover:bg-[#EFF9FE]
                                        hover:text-[#0788CF]
                                    "
                                >
                                    Configuración
                                </Link>

                                <div className="border-t border-[#D9EEF8]" />

                                <button
                                    type="button"
                                    onClick={handleLogout}
                                    className="
                                        w-full
                                        text-left
                                        px-4
                                        py-3
                                        text-sm
                                        text-red-500
                                        hover:bg-red-50
                                    "
                                >
                                    Cerrar sesión
                                </button>

                            </div>
                        )}

                    </div>

                </div>

            </div>


            {/* =====================================================
                MENÚ MÓVIL
            ====================================================== */}

            {menuOpen && (
                <div
                    className="
                        md:hidden
                        bg-[#003B70]
                        px-5
                        py-4
                        space-y-1
                        shadow-lg
                    "
                >

                    <Link
                        to="/dashboard"
                        onClick={closeAll}
                        className="
                            block
                            py-2.5
                            text-white
                            text-sm
                        "
                    >
                        Dashboard
                    </Link>

                    <div className="border-t border-white/20 my-2" />

                    <Link
                        to="/users"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Usuarios
                    </Link>

                    <Link
                        to="/roles"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Roles
                    </Link>

                    <Link
                        to="/socios"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Socios
                    </Link>

                    <Link
                        to="/medidores"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Medidores
                    </Link>

                    <Link
                        to="/lecturas"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Lecturas
                    </Link>

                    <Link
                        to="/tarifas"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Tarifas
                    </Link>

                    <Link
                        to="/facturas"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Facturas
                    </Link>

                    <Link
                        to="/pagos"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Pagos
                    </Link>

                    <Link
                        to="/notificaciones"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Notificaciones
                    </Link>

                    <div className="border-t border-white/20 my-2" />

                    <Link
                        to="/reportes/ingresos"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Ingresos
                    </Link>

                    <Link
                        to="/reportes/deudores"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Deudores
                    </Link>

                    <Link
                        to="/reportes/consumo"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Consumo
                    </Link>

                    <div className="border-t border-white/20 my-2" />

                    <Link
                        to="/perfil"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Mi perfil
                    </Link>

                    <Link
                        to="/configuracion"
                        onClick={closeAll}
                        className="block py-2.5 text-white text-sm"
                    >
                        Configuración
                    </Link>

                    <button
                        type="button"
                        onClick={handleLogout}
                        className="
                            block
                            py-2.5
                            text-red-200
                            text-sm
                        "
                    >
                        Cerrar sesión
                    </button>

                </div>
            )}

        </header>
    );
};


/* =============================================================
   OPCIONES DEL MENÚ ADMINISTRACIÓN
============================================================= */

interface AdminLinkProps {
    to: string;
    icon: string;
    text: string;
    onClick: () => void;
}

const AdminLink: React.FC<AdminLinkProps> = ({
    to,
    icon,
    text,
    onClick,
}) => {
    return (
        <Link
            to={to}
            onClick={onClick}
            className="
                flex
                items-center
                gap-3
                px-4
                h-[36px]
                text-[11px]
                text-[#526779]
                hover:bg-[#EFF9FE]
                hover:text-[#0788CF]
                transition-colors
            "
        >
            <span className="text-[#0788CF] w-4 text-center">
                {icon}
            </span>

            {text}
        </Link>
    );
};


/* =============================================================
   OPCIONES DEL MENÚ REPORTES
============================================================= */

interface ReportLinkProps {
    to: string;
    icon: string;
    text: string;
    onClick: () => void;
}

const ReportLink: React.FC<ReportLinkProps> = ({
    to,
    icon,
    text,
    onClick,
}) => {
    return (
        <Link
            to={to}
            onClick={onClick}
            className="
                flex
                items-center
                gap-3
                px-4
                h-[38px]
                text-[11px]
                text-[#526779]
                hover:bg-[#EFF9FE]
                hover:text-[#0788CF]
                transition-colors
            "
        >
            <span className="text-[#0788CF] w-4 text-center">
                {icon}
            </span>

            {text}
        </Link>
    );
};

export default Navbar;