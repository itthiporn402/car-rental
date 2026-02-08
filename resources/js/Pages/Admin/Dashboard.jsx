import React from "react";
import { Card, CardContent } from "@/components/ui/card.jsx";
import { usePage } from "@inertiajs/inertia-react";

export default function Dashboard() {
    const { stats } = usePage().props;

    return (
        <div className="container mx-auto p-6">
            <h1 className="text-2xl font-bold mb-4">Admin Dashboard</h1>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Card>
                    <CardContent>
                        <h2 className="text-xl font-semibold">Total Bookings</h2>
                        <p className="text-3xl font-bold">{stats.totalBookings}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent>
                        <h2 className="text-xl font-semibold">Confirmed Bookings</h2>
                        <p className="text-3xl font-bold text-green-500">{stats.confirmedBookings}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent>
                        <h2 className="text-xl font-semibold">Cancelled Bookings</h2>
                        <p className="text-3xl font-bold text-red-500">{stats.cancelledBookings}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
