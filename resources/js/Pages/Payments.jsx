import React from "react";
import { useForm, usePage } from "@inertiajs/react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "react-hot-toast";
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

// ฟังก์ชันแปลงวันที่ให้เป็น "19 กุมภาพันธ์ 2025"
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("th-TH", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};

const Payments = () => {
    const { booking, car, user } = usePage().props;
    const { post, processing } = useForm({
        booking_id: booking.id,
        user_id: user.id,
        amount: booking.total_amount,
        payment_method: "Credit Card", // Mockup วิธีจ่ายเงิน
    });

    const handlePayment = () => {
        post(route("payments.store"), {
            onSuccess: () => {
                Swal.fire({
                    title: "🎉 ชำระเงินสำเร็จ!",
                    text: "ขอบคุณที่ใช้บริการ 🚗✨",
                    icon: "success",
                    confirmButtonColor: "#16a34a",
                    confirmButtonText: "ตกลง",
                });
            },
        });
    };

    return (
        <div className="max-w-lg mx-auto p-6">
            <h1 className="text-3xl font-bold text-center bg-gradient-to-r from-green-500 to-blue-500 text-transparent bg-clip-text mb-6">
                💳 ชำระเงิน
            </h1>

            <Card className="shadow-xl rounded-2xl p-6 bg-white border border-gray-200">
                <CardContent className="text-center">
                    <h2 className="text-xl font-semibold text-gray-700">
                        🚗 รถที่จอง: {car.brand} {car.model}
                    </h2>
                    <p className="text-gray-500 mt-2">
                        📆 วันที่รับ: {formatDate(booking.start_date)}
                    </p>
                    <p className="text-gray-500">
                        📆 วันที่คืน: {formatDate(booking.end_date)}
                    </p>

                    <p className="text-2xl font-bold text-green-600 mt-4">
                        💰 ฿{booking.total_amount.toLocaleString()}
                    </p>

                    <Button
                        onClick={handlePayment}
                        isLoading={processing}
                        className="w-full mt-6 py-3 bg-green-500 text-white font-bold hover:bg-green-600 transition-all rounded-lg shadow-lg"
                    >
                        ✅ จ่ายเงิน
                    </Button>
                </CardContent>
            </Card>
        </div>
    );
};

Payments.layout = (page) => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
export default Payments;
