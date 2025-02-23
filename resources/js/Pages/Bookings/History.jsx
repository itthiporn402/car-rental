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

    const handleReview = (bookingId, carId) => {
        console.log("Booking ID:", bookingId); // ตรวจสอบ bookingId
        console.log("Car ID:", carId); // ตรวจสอบ carId

        if (!carId) {
            Swal.fire(
                "❌ ไม่มีข้อมูลรถ",
                "ไม่สามารถส่งรีวิวได้เนื่องจากข้อมูลรถไม่สมบูรณ์",
                "error"
            );
            return;
        }

        // ตรวจสอบสถานะการรีวิวจากเซิร์ฟเวอร์
        axios
            .get(
                route("reviews.check", { booking_id: bookingId, car_id: carId })
            )
            .then((response) => {
                if (response.data.reviewed) {
                    Swal.fire(
                        "📜 คุณเคยรีวิวไปแล้ว",
                        "คุณได้ให้คะแนนและความคิดเห็นเกี่ยวกับการจองนี้แล้ว",
                        "info"
                    );
                    return; // ไม่ให้รีวิวใหม่
                }

                // แสดงฟอร์มให้รีวิว
                Swal.fire({
                    title: "🌟 ให้คะแนนการจองของคุณ",
                    html: `
                        <div class="flex items-center justify-center mb-4">
                            <span>⭐</span>
                            <div id="rating-container" class="flex cursor-pointer">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                            <span id="rating-message" class="ml-2">เลือกคะแนน (1-5)</span>
                        </div>
                        <textarea id="comment" rows="4" class="w-full p-2 border border-gray-300 rounded-lg" placeholder="เขียนความคิดเห็นของคุณ"></textarea>
                    `,
                    showCancelButton: true,
                    confirmButtonText: "ส่งรีวิว",
                    cancelButtonText: "ยกเลิก",
                    preConfirm: () => {
                        const rating = document.querySelectorAll(
                            ".star.text-yellow-400"
                        );
                        const comment =
                            document.getElementById("comment").value;

                        console.log("Rating:", rating.length); // นับจำนวนดาวที่เลือก
                        console.log("Comment:", comment);

                        if (rating.length === 0 || !comment) {
                            Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบ");
                            return false;
                        }

                        // ส่งคะแนนตามจำนวนดาวที่เลือก
                        return { rating: rating.length, comment };
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        const { rating, comment } = result.value;

                        // ส่งข้อมูลรีวิวไปยังเซิร์ฟเวอร์
                        axios
                            .post(route("reviews.store"), {
                                booking_id: bookingId,
                                car_id: carId,
                                rating: rating,
                                comment: comment,
                            })
                            .then((response) => {
                                Swal.fire(
                                    "ขอบคุณสำหรับการรีวิว!",
                                    "",
                                    "success"
                                );
                            })
                            .catch((error) => {
                                if (
                                    error.response &&
                                    error.response.data.errors
                                ) {
                                    console.log(error.response.data.errors);
                                }
                                Swal.fire(
                                    "เกิดข้อผิดพลาด",
                                    "ไม่สามารถส่งรีวิวได้",
                                    "error"
                                );
                            });
                    }
                });

                // การคลิกเลือกดาว
                const stars = document.querySelectorAll(".star");
                stars.forEach((star) => {
                    star.addEventListener("click", () => {
                        const ratingValue = star.dataset.value;
                        document.querySelectorAll(".star").forEach((s) => {
                            s.classList.remove("text-yellow-400");
                        });
                        // เพิ่มสีเหลืองให้กับดาวที่เลือก
                        for (let i = 0; i < ratingValue; i++) {
                            stars[i].classList.add("text-yellow-400");
                        }
                    });
                });
            })
            .catch((error) => {
                console.error(
                    "เกิดข้อผิดพลาดในการตรวจสอบสถานะการรีวิว:",
                    error
                );
                Swal.fire(
                    "เกิดข้อผิดพลาด",
                    "ไม่สามารถตรวจสอบสถานะการรีวิวได้",
                    "error"
                );
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
                                <th className="p-3">⭐ รีวิว</th>
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
                                        {booking.status === "ชำระเงินแล้ว" && (
                                            <button
                                                onClick={() =>
                                                    handleReview(
                                                        booking.id,
                                                        booking.car.id
                                                    )
                                                }
                                                className="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-yellow-600 transition-all"
                                            >
                                                ⭐ รีวิว
                                            </button>
                                        )}
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

const getStatusColor = (status) => {
    return status === "ชำระเงินแล้ว"
        ? "text-green-600 bg-green-100"
        : "text-red-600 bg-red-100";
};

BookingHistory.layout = (page) => (
    <AuthenticatedLayout>{page}</AuthenticatedLayout>
);

export default BookingHistory;
