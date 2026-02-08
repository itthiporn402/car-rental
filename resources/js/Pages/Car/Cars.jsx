import React, { useState } from "react";
import { Link, usePage } from "@inertiajs/react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Users, Car, Wrench, Search } from "lucide-react";

export default function Cars() {
    const { cars = [] } = usePage().props;  // ดึงข้อมูลรถทั้งหมด
    const [search, setSearch] = useState(""); // ใช้ useState("") เพื่อเก็บค่าการค้นหารถ

    //ฟังก์ชันแสดงไอคอนตามสถานะของรถ
    const getStatusIcon = (status) => {
        const icons = {
            available: (
                <Car className="w-5 h-5 text-green-500" title="พร้อมให้เช่า" />
            ),
            rented: (
                <Users
                    className="w-5 h-5 text-yellow-500"
                    title="ถูกเช่าแล้ว"
                />
            ),
            maintenance: (
                <Wrench className="w-5 h-5 text-red-500" title="ซ่อมบำรุง" />
            ),
        };
        return icons[status] || null;
    };
    // ฟังก์ชันกรองรถตามคำค้นหา
    const filteredCars = cars.filter((car) => { // กรองรถตามคำค้นหาตัวพิมพ์เล็ก
        const query = search.toLowerCase();
        return (
            car.name?.toLowerCase().includes(query) || //ยี่ห้อรถ
            car.brand?.toLowerCase().includes(query)
        );
    });

    return ( // ส่วนของการแสดงผล
        <div className="container mx-auto p-6">
            <h1 className="text-4xl font-extrabold text-center mb-8 animate-rainbow">
                🚗 GoodCar
            </h1>   

            <div className="flex justify-center mb-8">
                <div className="relative w-full max-w-md">
                    <Search className="absolute left-3 top-2.5 text-gray-400 w-5 h-5" />
                    <input //กล่องค้นหา (input)
                        type="text"
                        placeholder="🔍 ค้นหารถ..."
                        className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                </div>
            </div>

            {filteredCars.length === 0 ? (
                <p className="text-center text-gray-500 text-lg">
                    ❌ ไม่พบรถที่ตรงกับคำค้นหา
                </p>
            ) : (
                //แสดงรายการรถยนต์
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    {filteredCars.map((car) => (
                        <Card
                            key={car.id}
                            className="shadow-xl rounded-2xl overflow-hidden bg-white transform hover:scale-105 hover:shadow-2xl transition-all duration-300"
                        >
                            <CardContent className="p-4">
                                <div className="relative">
                                    {car.image ? (
                                        <img
                                            src={car.image}
                                            alt={car.model}
                                            className="w-full h-48 object-cover rounded-lg"
                                        />
                                    ) : (
                                        <div className="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                                            ไม่มีรูปภาพ
                                        </div>
                                    )}
                                    <span className="absolute top-2 right-2 flex items-center gap-1 bg-white shadow-md px-3 py-1 rounded-full text-sm font-medium">
                                        {getStatusIcon(car.status)}
                                        {car.status === "available"
                                            ? "พร้อมให้เช่า"
                                            : car.status === "rented"
                                            ? "ถูกเช่าแล้ว"
                                            : "ซ่อมบำรุง"}
                                    </span>
                                </div>

                                <h2 className="text-xl font-bold text-gray-800 mt-3">
                                    {car.brand} {car.model}
                                </h2>

                                <div className="flex items-center gap-2 text-gray-600 text-sm my-2">
                                    <Users className="w-5 h-5 text-blue-500" />
                                    <span>{car.seats} ที่นั่ง</span>
                                </div>

                                <p className="text-gray-700 font-semibold text-lg mb-3">
                                    ฿
                                    {new Intl.NumberFormat().format(
                                        car.daily_rate
                                    )}{" "}
                                    / วัน
                                </p>

                                {car.status === "available" ? (
                                    <Link
                                        href={route("bookings.create", {
                                            car: car.id,
                                        })}
                                    >
                                        <Button className="w-full py-2 text-white font-semibold rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-indigo-600 hover:to-blue-500 shadow-md transform hover:-translate-y-1 transition-all duration-300">
                                            🚀 จองตอนนี้
                                        </Button>
                                    </Link>
                                ) : (
                                    <span className="block text-center text-gray-500 text-sm font-medium">
                                        🚫 ไม่พร้อมให้เช่า
                                    </span>
                                )}
                            </CardContent>
                        </Card>
                    ))}
                </div>
            )}
        </div>
    );
}

Cars.layout = (page) => <AuthenticatedLayout>{page}</AuthenticatedLayout>; // กำหนด Layout ให้กับหน้านี้
