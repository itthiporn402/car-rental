import React from "react";
import { Head } from "@inertiajs/react";

export default function AppLayout({ children }) {
    return (
        <div className="min-h-screen bg-gray-100">
            <Head title="Car Rental" />
            <header className="bg-blue-600 text-white p-4 text-center">
                <h1 className="text-lg font-bold">Car Rental System</h1>
            </header>
            <main className="p-6">{children}</main>
        </div>
    );
}
