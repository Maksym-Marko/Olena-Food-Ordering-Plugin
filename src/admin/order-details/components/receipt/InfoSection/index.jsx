import { InfoItem } from "@orderDetails/components/receipt/InfoItem";

export const InfoSection = ({ title, items }) => (
    <div className="receipt-section">
        <div className="section-title">{title}</div>
        <div className="info-grid">
            {items.map((item, index) => (
                <InfoItem key={index} {...item} />
            ))}
        </div>
    </div>
);