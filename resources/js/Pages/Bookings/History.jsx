import React, { useState } from "react";
import { Link, useForm } from "@inertiajs/react";
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

const BookingHistory = ({ bookings: initialBookings }) => {
    const [bookings, setBookings] = useState(initialBookings);
    const { delete: destroy, processing } = useForm();

    const handleCancel = (id) => {
        Swal.fire({
            title: "⚠️ ยืนยันการยกเลิก?",
            text: "คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองนี้? ⛔",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "ใช่, ยกเลิกเลย!",
            cancelButtonText: "ไม่, กลับไป",
        }).then((result) => {
            if (result.isConfirmed) {
                destroy(route("bookings.destroy", id), {
                    onSuccess: () => {
                        // ลบออกจากฐานข้อมูลสำเร็จ => อัปเดต UI
                        setBookings((prev) =>
                            prev.filter((booking) => booking.id !== id)
                        );
                        Swal.fire({
                            title: "✅ ยกเลิกสำเร็จ!",
                            text: "การจองของคุณถูกลบออกจากระบบแล้ว",
                            icon: "success",
                        });
                    },
                    onError: () =>
                        Swal.fire({
                            title: "❌ ล้มเหลว!",
                            text: "ไม่สามารถยกเลิกการจองได้ กรุณาลองใหม่อีกครั้ง",
                            icon: "error",
                        }),
                });
            }
        });
    };

    return (
        <div className="max-w-5xl mx-auto p-8 bg-white shadow-2xl rounded-2xl">
            <h2 className="text-3xl font-extrabold text-center mb-6 bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                📜 ประวัติการเช่ารถ
            </h2>

            {bookings.length === 0 ? (
                <p className="text-center text-gray-500 text-lg">
                    ❌ คุณยังไม่มีประวัติการเช่า
                </p>
            ) : (
                <div className="overflow-x-auto">
                    <table className="w-full border border-gray-300 bg-white shadow-lg rounded-lg overflow-hidden">
                        <thead>
                            <tr className="bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-center">
                                <th className="p-3">🚗 รถ</th>
                                <th className="p-3">🖼️ รูปภาพ</th>
                                <th className="p-3">📅 วันที่เริ่ม</th>
                                <th className="p-3">📅 วันที่คืน</th>
                                <th className="p-3">💳 สถานะ</th>
                                <th className="p-3">🔍 รายละเอียด</th>
                                <th className="p-3">❌ ยกเลิก</th>
                            </tr>
                        </thead>
                        <tbody>
                            {bookings.map((booking, index) => (
                                <tr
                                    key={booking.id}
                                    className={`text-center ${
                                        index % 2 === 0
                                            ? "bg-gray-50"
                                            : "bg-white"
                                    } hover:bg-gray-100 transition-all`}
                                >
                                    <td className="p-3 font-semibold text-gray-800">
                                        {booking.car.name}
                                    </td>

                                    <td className="p-3">
                                        {booking.car.image ? (
                                            <img
                                                src={booking.car.image}
                                                alt={booking.car.name}
                                                className="w-24 h-16 object-cover rounded-lg shadow-md border"
                                            />
                                        ) : (
                                            <span className="text-gray-400">
                                                ไม่มีรูป
                                            </span>
                                        )}
                                    </td>

                                    <td className="p-3 text-gray-700">
                                        {new Date(
                                            booking.start_date
                                        ).toLocaleDateString()}
                                    </td>
                                    <td className="p-3 text-gray-700">
                                        {new Date(
                                            booking.end_date
                                        ).toLocaleDateString()}
                                    </td>

                                    <td
                                        className={`p-3 font-bold ${getStatusColor(
                                            booking.status
                                        )} rounded-lg shadow-md py-1`}
                                    >
                                        {booking.status}
                                    </td>

                                    <td className="p-3">
                                        <Link
                                            href={`/bookings/${booking.id}`}
                                            className="text-blue-600 font-semibold hover:underline transition-all"
                                        >
                                            🔍 ดูรายละเอียด
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {booking.status !== "ชำระเงินแล้ว" && (
                                            <button
                                                onClick={() =>
                                                    handleCancel(booking.id)
                                                }
                                                className="bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-red-600 transition-all disabled:opacity-50"
                                                disabled={processing}
                                            >
                                                ❌ ยกเลิก
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
};

// ฟังก์ชันกำหนดสีสถานะ
const getStatusColor = (status) => {
    return status === "ชำระเงินแล้ว"
        ? "text-green-600 bg-green-100"
        : "text-red-600 bg-red-100";
};

BookingHistory.layout = (page) => (
    <AuthenticatedLayout>{page}</AuthenticatedLayout>
);

export default BookingHistory;
