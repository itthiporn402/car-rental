import React from "react";
import { useForm, usePage } from "@inertiajs/react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Input from "@/components/ui/input";
import Swal from "sweetalert2";
import { toast } from "react-hot-toast";

const Booking = () => {
    const { car, user } = usePage().props;

    if (!car || !user) {
        return (
            <p className="text-center text-red-500">❌ ไม่พบข้อมูลรถหรือผู้ใช้</p>
        );
    }

    const { data, setData, post, processing, errors } = useForm({
        car_id: car.id,
        user_id: user.id,
        pickup_date: "",
        return_date: "",
    });

    const handleBooking = (e) => {
        e.preventDefault();

        if (!data.pickup_date || !data.return_date) {
            Swal.fire({
                icon: "warning",
                title: "⚠️ กรุณาเลือกวันที่รับและคืนรถ",
                confirmButtonColor: "#f59e0b",
            });
            return;
        }

        if (data.pickup_date > data.return_date) {
            Swal.fire({
                icon: "error",
                title: "🚨 วันที่คืนรถต้องไม่น้อยกว่าวันที่รับรถ",
                confirmButtonColor: "#dc2626",
            });
            return;
        }

        post(route("bookings.store"), {
            onStart: () => console.log("🚀 Start Booking"),
            onSuccess: () => {
                toast.success("🎉 จองรถสำเร็จ!");
                Swal.fire({
                    icon: "success",
                    title: "✅ เลือกสำเร็จ กรุณาจ่ายเงิน!",
                    confirmButtonColor: "#10b981",
                });
            },
            onError: (errors) => {
                Swal.fire({
                    icon: "error",
                    title: "❌ มีข้อผิดพลาด",
                    text: Object.values(errors).join("\n"),
                    confirmButtonColor: "#ef4444",
                });
            },
            onFinish: () => console.log("✅ Finish Booking"),
        });
    };

    return (
        <div className="max-w-lg mx-auto p-6">
            <h1 className="text-3xl font-bold text-center bg-gradient-to-r from-green-500 to-blue-500 text-transparent bg-clip-text mb-6">
                🚗 จองรถ {car.name}
            </h1>
            <Card className="shadow-xl rounded-2xl overflow-hidden border border-gray-200">
                <img
                    src={car.image}
                    alt={`${car.brand} ${car.model}`}
                    className="w-full h-48 object-cover"
                />

                <CardContent className="p-6 bg-gray-50">
                    <div className="mb-6 text-center">
                        <h2 className="text-2xl font-semibold text-gray-900">
                            {car.name}
                        </h2>
                        <p className="text-gray-500">
                            💰 ฿{car?.daily_rate?.toLocaleString() || "N/A"} / วัน
                        </p>
                    </div>
                    <hr className="my-4 border-gray-300" />
                    <form onSubmit={handleBooking} className="space-y-4">
                        <div>
                            <label className="block font-medium">📅 วันที่รับรถ:</label>
                            <Input
                                type="date"
                                min={new Date().toISOString().split("T")[0]} // ✅ ป้องกันเลือกวันที่ย้อนหลัง
                                value={data.pickup_date}
                                onChange={(e) => setData("pickup_date", e.target.value)}
                            />
                            {errors.pickup_date && (
                                <p className="text-red-500 text-sm">{errors.pickup_date}</p>
                            )}
                        </div>
                        <div>
                            <label className="block font-medium">📆 วันที่คืนรถ:</label>
                            <Input
                                type="date"
                                min={
                                    data.pickup_date || new Date().toISOString().split("T")[0]
                                } // ✅ ป้องกันคืนก่อนรับ
                                value={data.return_date}
                                onChange={(e) => setData("return_date", e.target.value)}
                            />
                            {errors.return_date && (
                                <p className="text-red-500 text-sm">{errors.return_date}</p>
                            )}
                        </div>
                        <Button
                            type="submit"
                            disabled={processing} // ✅ ใช้ disabled แทน
                            className={`w-full py-3 font-semibold bg-gradient-to-r from-blue-500 to-indigo-600
                                hover:from-indigo-600 hover:to-blue-500 text-white shadow-lg transform transition duration-300
                                ${processing ? "opacity-50 cursor-not-allowed" : "hover:-translate-y-1"}
                            `}
                        >
                            {processing ? "⏳ กำลังจอง..." : "✅ ยืนยันการจอง"}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    );
};

Booking.layout = (page) => <AuthenticatedLayout>{page}</AuthenticatedLayout>;

export default Booking;
