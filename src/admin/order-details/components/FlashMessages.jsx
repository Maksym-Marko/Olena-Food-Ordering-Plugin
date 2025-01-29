import { useSelector, useDispatch } from "react-redux"
import { useEffect } from "react"
import { clearSuccess, clearWarnings, clearErrors } from "@orderDetails/store/slices/notify/notifySlice"
import { FlashBox } from "./FlashBox"

const expTime = {
    successTime: null,
    warningTime: null,
    errorTime: null,
}

const lifePeriod = 10000

const FlashMessages = () => {

    const dispatch = useDispatch()

    const success = useSelector(state => state.notify.success)
    const warnings = useSelector(state => state.notify.warnings)
    const errors = useSelector(state => state.notify.errors)

    const emptySuccess = () => {

        if (success.length === 0) return

        clearTimeout(expTime.successTime)

        expTime.successTime = setTimeout(() => {

            dispatch(clearSuccess())
        }, lifePeriod)
    }

    const emptyWarnings = () => {

        if (warnings.length === 0) return

        clearTimeout(expTime.warningTime)

        expTime.warningTime = setTimeout(() => {

            dispatch(clearWarnings())
        }, lifePeriod)
    }

    const emptyErrors = () => {

        if (errors.length === 0) return

        clearTimeout(expTime.errorTime)

        expTime.errorTime = setTimeout(() => {

            dispatch(clearErrors())
        }, lifePeriod)
    }

    useEffect(() => {

        emptySuccess()
        emptyWarnings()
        emptyErrors()

    }, [success, warnings, errors])

    const handleSuccessClose = (type, index) => {

        dispatch(clearSuccess({index, type}))
    }

    const handleWarningsClose = (type, index) => {

        dispatch(clearWarnings({index, type}))
    }

    const handleErrorsClose = (type, index) => {

        dispatch(clearErrors({index, type}))
    }

    return (success.length > 0 || warnings.length > 0 || errors.length > 0 ?
        <div
            style={{
                position: 'fixed',
                right: '20px',
                bottom: '20px'
            }}
        >

            <div>

                {
                    success.length > 0 &&
                    success.map((message, index) => (

                        <FlashBox key={`success-${index}`} index={index} className="fo-success" onClose={() => handleSuccessClose('success', index)}>
                            {message}
                        </FlashBox>
                    ))
                }

                {
                    warnings.length > 0 &&
                    warnings.map((warning, index) => (

                        <FlashBox key={`warning-${index}`} index={index} className="fo-warning" onClose={() => handleWarningsClose('warnings', index)}>
                            {warning}
                        </FlashBox>
                    ))
                }

                {
                    errors.length > 0 &&
                    errors.map((error, index) => (

                        <FlashBox key={`error-${index}`} index={index} className="fo-error" onClose={() => handleErrorsClose('errors', index)}>
                            {error}
                        </FlashBox>                        
                    ))
                }
            </div>

        </div> :
        ''
    )
}

export default FlashMessages