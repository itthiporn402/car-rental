import React from "react";
import { router, usePage } from "@inertiajs/react";

export default function Bookings() {
  const { bookings } = usePage().props;

  const approveBooking = (id) => {
    router.patch(route("admin.bookings.approve", id));
  };

  const rejectBooking = (id) => {
    router.patch(route("admin.bookings.reject", id));
  };

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold mb-4">รายการการจอง</h1>
      <table className="w-full border-collapse border border-gray-300">
        <thead>
          <tr>
            <th className="border p-2">รถ</th>
            <th className="border p-2">วันที่เริ่ม</th>
            <th className="border p-2">วันที่สิ้นสุด</th>
            <th className="border p-2">สถานะ</th>
            <th className="border p-2">จัดการ</th>
          </tr>
        </thead>
        <tbody>
          {bookings.map((booking) => (
            <tr key={booking.id}>
              <td className="border p-2">{booking.car.name}</td>
              <td className="border p-2">{booking.start_date}</td>
              <td className="border p-2">{booking.end_date}</td>
              <td className="border p-2">{booking.status}</td>
              <td className="border p-2">
                {booking.status === "pending" && (
                  <>
                    <button
                      onClick={() => approveBooking(booking.id)}
                      className="bg-green-500 text-white px-2 py-1 rounded"
                    >
                      อนุมัติ
                    </button>
                    <button
                      onClick={() => rejectBooking(booking.id)}
                      className="bg-red-500 text-white px-2 py-1 rounded ml-2"
                    >
                      ปฏิเสธ
                    </button>
                  </>
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
