import React from 'react';
import Navbar from './Navbar';
import Footer from './Footer';

interface LayoutProps {
    children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
    return (
        <div className="min-h-screen flex flex-col bg-sky-50">

            {/* Barra superior */}
            <Navbar />

            {/* Contenido principal */}
            <main className="flex-1 container mx-auto px-6 py-6">
                {children}
            </main>

            {/* Pie de página */}
            <Footer />

        </div>
    );
};

export default Layout;