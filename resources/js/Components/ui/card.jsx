// import { Card, CardContent } from "../../Components/ui/card";

export function Card({ children }) {
    return <div className="border rounded-lg p-4 shadow">{children}</div>;
}

export function CardContent({ children }) {
    return <div className="mt-2">{children}</div>;
}
