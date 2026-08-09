import React from 'react';
import Navbar from './Navbar';
import Footer from './Footer';

interface LayoutProps {
    children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
    return (
        <div className="min-h-screen flex flex-col bg-[#EFF9FE]">

            {/* =========================================
                BARRA SUPERIOR
            ========================================= */}
            <Navbar />


            {/* =========================================
                CONTENIDO PRINCIPAL
            ========================================= */}

            <main className="flex-1 bg-[#EFF9FE]">

                {children}

            </main>


            {/* =========================================
                PIE DE PÁGINA
            ========================================= */}

            <Footer />

        </div>
    );
};

export default Layout;