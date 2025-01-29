import { useSelector } from 'react-redux';
import { __ } from '@wordpress/i18n';
import { useGetReceiptQuery } from '@orderDetails/services/Receipt';
import { getCurrencySymbol } from '@orderDetails/helpers';
import { InfoItem } from '@orderDetails/components/receipt/InfoItem';
import { InfoSection } from '@orderDetails/components/receipt/InfoSection';
import { OrderItem } from '@orderDetails/components/receipt/OrderItem';
import { OrderSummary } from '@orderDetails/components/receipt/OrderSummary';

const Receipt = () => {
    const { data: response, isLoading, error } = useGetReceiptQuery();

	const order = response?.orderData;

	const orderId = response?.orderId;

    if (!order) return null;

    const calculateOrderSummary = () => {
        const subtotal = order.items.reduce((sum, item) => {
            const itemTotal = item.price_per_unit * item.quantity;
            const addonsTotal = item.selected_add_ons?.reduce((sum, addon) => 
                sum + (addon.price * addon.quantity), 0) || 0;
            return sum + itemTotal + addonsTotal;
        }, 0);

        return {
            'Subtotal': subtotal,
            'Delivery Fee': order.deliveryData.fee,
            'total': subtotal + order.deliveryData.fee
        };
    };

    return (
        <div className="ofo-receipt-container">
            <div className="receipt-header">
                <div className="receipt-title">{__('Order Confirmation', 'olena-food-ordering')}</div>
                <div className="order-number">
                    {__('Order #', 'olena-food-ordering')}{orderId} - {new Date(order.orderPlaced.utc).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false })}
                </div>
            </div>

            <InfoSection 
                title={__('Customer Information', 'olena-food-ordering')}
                items={[
                    { label: __('First Name', 'olena-food-ordering'), value: order?.customerData?.firstName },
                    { label: __('Last Name', 'olena-food-ordering'), value: order?.customerData?.lastName },
                    { label: __('Email', 'olena-food-ordering'), value: order?.customerData?.email },
                    { label: __('Phone', 'olena-food-ordering'), value: order?.customerData?.phone }
                ]}
            />

            <InfoSection 
                title={__('Delivery Information', 'olena-food-ordering')}
                items={[
                    { label: __('Street Address', 'olena-food-ordering'), value: order?.deliveryData?.address?.street || "---" },
                    { label: __('City', 'olena-food-ordering'), value: order?.deliveryData?.address?.city || "---" },
                    { label: __('Postal Code', 'olena-food-ordering'), value: order?.deliveryData?.address?.postalCode || "---" },
                    { label: __('Delivery Method', 'olena-food-ordering'), value: (order?.deliveryData?.method?.charAt(0).toUpperCase() + order?.deliveryData?.method?.slice(1)) || "---" }
                ]}
            />

            <div className="receipt-section">
                <div className="section-title">{__('Order Details', 'olena-food-ordering')}</div>
                <div className="order-items">
                    {order.items.map((item, index) => (
                        <OrderItem 
                            key={index}
                            {...item}
                        />
                    ))}
                </div>
                <OrderSummary summary={calculateOrderSummary()} />
            </div>

            <div className="receipt-section">
                <div className="section-title">{__('Payment Information', 'olena-food-ordering')}</div>
                <InfoItem 
                    label={__('Payment Method', 'olena-food-ordering')}
                    value={order.paymentData.method}
                />
            </div>
        </div>
    );
};

export default Receipt;