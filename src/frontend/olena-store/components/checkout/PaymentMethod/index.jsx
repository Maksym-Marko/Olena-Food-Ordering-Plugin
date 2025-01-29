export const PaymentMethod = ({ title, description, isSelected, onChange }) => {
    const paymentId = `payment-${title.toLowerCase().replace(/\s+/g, '-')}`;
    return (
      <label htmlFor={paymentId} className={`payment-method ${isSelected ? 'selected' : ''}`}>
        <input
          id={paymentId}
          type="radio"
          name="payment"
          checked={isSelected}
          onChange={onChange}
        />
        <div className="delivery-details">
          <div className="delivery-title">{title}</div>
          <div className="delivery-description">{description}</div>
        </div>
      </label>
    );
};