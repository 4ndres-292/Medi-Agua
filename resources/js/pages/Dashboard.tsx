import React, { useEffect, useState } from 'react';
import api from '../services/api';
import Layout from '../components/layout/Layout';
 
interface DashboardStats {
    socios: number;
    medidores: number;
    pagos: number;
    facturas: number;
    lecturas: number;
    consumo: number;
}
 
const Dashboard: React.FC = () => {
 
    const [stats, setStats] = useState<DashboardStats>({
        socios: 0,
        medidores: 0,
        pagos: 0,
        facturas: 0,
        lecturas: 0,
        consumo: 0,
    });
 
    useEffect(() => {
        fetchStats();
    }, []);
 
    const fetchStats = async () => {
 
        try {
 
            const [
                sociosRes,
                medidoresRes,
                pagosRes,
                facturasRes,
            ] = await Promise.all([
                api.get('/socios'),
                api.get('/medidores'),
                api.get('/pagos'),
                api.get('/facturas'),
            ]);
 
            setStats({
                socios: sociosRes.data.data?.length ?? 0,
                medidores: medidoresRes.data.data?.length ?? 0,
                pagos: pagosRes.data.data?.length ?? 0,
                facturas: facturasRes.data.data?.length ?? 0,
                lecturas: 0,
                consumo: 0,
            });
 
        } catch (error) {
 
            console.error(
                'Error al cargar estadísticas:',
                error
            );
 
        }
    };
 
 
    return (
 
        <Layout>
 
            {/* =====================================================
                DASHBOARD
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
 
 
                    {/* TITULO */}
 
                    <div className="mb-10">
 
                        <h1 className="
                            text-4xl
                            font-bold
                            text-[#12345B]
                            drop-shadow-sm
                        ">
                            ¡Bienvenido de vuelta!
                        </h1>
 
                        <p className="
                            mt-2
                            text-gray-600
                            text-lg
                            drop-shadow-sm
                        ">
                            Aquí tienes un resumen general del sistema.
                        </p>
 
                    </div>
 
 
                    {/* =================================================
                        CARDS
                    ================================================== */}
 
                    <div className="
                        grid
                        grid-cols-1
                        sm:grid-cols-2
                        lg:grid-cols-5
                        gap-5
                    ">
 
 
                        {/* SOCIOS */}
                        <DashboardCard
                            title="SOCIOS"
                            value={stats.socios}
                            description="Total registrados"
                            icon="👥"
                        />
 
 
                        {/* MEDIDORES */}
                        <DashboardCard
                            title="MEDIDORES"
                            value={stats.medidores}
                            description="Total activos"
                            icon="◔"
                        />
 
 
                        {/* LECTURAS */}
                        <DashboardCard
                            title="LECTURAS"
                            value={stats.lecturas}
                            description="Este mes"
                            icon="▤"
                        />
 
 
                        {/* CONSUMO */}
                        <DashboardCard
                            title="CONSUMO"
                            value={`${stats.consumo} m³`}
                            description="Este mes"
                            icon="💧"
                        />
 
 
                        {/* FACTURAS */}
                        <DashboardCard
                            title="FACTURAS"
                            value={stats.facturas}
                            description="Pendientes"
                            icon="▧"
                        />
 
                    </div>
 
 
                    {/* =================================================
                        ACTIVIDADES
                    ================================================== */}
 
                    <div className="
                        mt-10
                        bg-white/95
                        border
                        border-[#D8ECF8]
                        rounded-2xl
                        shadow-[0_8px_30px_rgba(15,75,110,0.08)]
                        p-7
                    ">
 
                        <div className="
                            flex
                            items-center
                            justify-between
                        ">
 
                            <div>
 
                                <h2 className="
                                    text-xl
                                    font-bold
                                    text-[#12345B]
                                ">
                                    Últimas actividades
                                </h2>
 
                                <p className="
                                    text-gray-400
                                    mt-1
                                ">
                                    Resumen de las actividades recientes del sistema.
                                </p>
 
                            </div>
 
 
                            <div className="
                                w-12
                                h-12
                                rounded-full
                                bg-[#EFF9FE]
                                text-[#0284C7]
                                flex
                                items-center
                                justify-center
                            ">
                                ◷
                            </div>
 
                        </div>
 
 
                        <div className="
                            mt-6
                            h-[135px]
                            rounded-xl
                            bg-[#F4FBFF]
                            border
                            border-[#D8ECF8]
                            flex
                            flex-col
                            items-center
                            justify-center
                            text-center
                        ">
 
                            <div className="
                                text-4xl
                                mb-3
                            ">
                                💧
                            </div>
 
                            <p className="text-gray-500">
                                Aquí aparecerán las últimas lecturas,
                                pagos y movimientos registrados.
                            </p>
 
                        </div>
 
                    </div>
 
                </div>
 
            </section>
 
        </Layout>
    );
};
 
 
/* =====================================================
   CARD
===================================================== */
 
interface DashboardCardProps {
    title: string;
    value: number | string;
    description: string;
    icon: string;
}
 
const DashboardCard: React.FC<DashboardCardProps> = ({
    title,
    value,
    description,
    icon,
}) => {
 
    return (
 
        <div className="
            bg-white
            border
            border-[#D8ECF8]
            rounded-2xl
            p-6
            min-h-[220px]
            shadow-[0_8px_30px_rgba(15,75,110,0.07)]
            transition-all
            duration-300
            hover:-translate-y-1
            hover:shadow-[0_15px_35px_rgba(15,75,110,0.13)]
        ">
 
            <div className="
                w-16
                h-16
                rounded-full
                bg-[#EFF9FE]
                flex
                items-center
                justify-center
                text-3xl
                text-[#0284C7]
                mb-5
            ">
                {icon}
            </div>
 
 
            <p className="
                text-sm
                font-medium
                text-[#50677F]
                tracking-wide
            ">
                {title}
            </p>
 
 
            <p className="
                text-4xl
                font-bold
                text-[#12345B]
                mt-2
            ">
                {value}
            </p>
 
 
            <p className="
                text-sm
                text-[#8BA0B5]
                mt-3
            ">
                {description}
            </p>
 
        </div>
 
    );
};
 
export default Dashboard;