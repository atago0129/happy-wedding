import React from "react";
import ReactDom from "react-dom";
import Modal from 'react-modal';
import Request from 'superagent';

class LoadingModal extends React.Component {
    onRequestClose() {
        // nothing to do
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   className="modal loadingModal"
            />
        );
    }
}

class RsvpModal extends React.Component {
    _handleClick(e) {
        var status = e.currentTarget.dataset.status;
        this.props.requestRsvp(status);
        this.props.closeModal();
    }

    onRequestClose() {
        this.props.closeModal();
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   className="modal"
            >
                <div>
                    <ul className="content rsvpSelector">
                        <li><p data-status="2" className="rsvpPart" onClick={this._handleClick.bind(this)}>御出席</p></li>
                        <li><p data-status="1" className="rsvpNonPart" onClick={this._handleClick.bind(this)}>御欠席</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class AlertModal extends React.Component {
    _handleClick(e) {
        this.props.closeModal();
    }

    onRequestClose() {
        this.props.closeModal();
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   className="modal"
            >
                <div>
                    <h1 className="modalTitle">エラー</h1>
                    <p className="modalDescription">不明なエラーが発生しました。ページをリロードし、しばらくおいてから試してください。。</p>
                    <p className="modalDescription">何度もこのメッセージが表示される場合、お手数ですが新郎新婦までご連絡ください。</p>
                    <p className="modalDescription">エラーコード: {this.props.errorCode}</p>
                    <ul className="modalSelectButtons">
                        <li><p onClick={this._handleClick.bind(this)}>確認</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class MessageModal extends React.Component {
    onRequestClose() {
        // nothing to do
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   className="modal"
            >
                <div>
                    <h1 className="modalTitle">ご出欠登録完了</h1>
                    <p className="modalDescription">ご回答頂きありがとうございました。</p>
                    <p className="modalDescription">出欠のご確定は11月4日までにお願いいたします。</p>
                    <ul className="modalSelectButtons">
                        <li><a href={location.href + '/present'}><p>確認</p></a></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class Rsvp extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            rsvpModalDisplay: false,
            alertModalDisplay: false,
            messageModalDisplay: false,
            loadingModalDisplay: false,
            errorCode: 'unknown error',
            userStatus: parseInt(props.status)
        };
    }

    setUserStatus(status) {
        this.setState({
            userStatus: status
        });
    }

    openModal() {
        this.setState({
            rsvpModalDisplay: true
        });
    }

    closeModal() {
        this.setState({
            rsvpModalDisplay: false
        });
    }

    openAlertModal() {
        this.setState({
            alertModalDisplay: true
        });
    }

    setAlertModalErrorCode(errorCode) {
        this.setState({
            errorCode: errorCode
        });
    }

    closeAlertModal() {
        this.setState({
            alertModalDisplay: false
        });
    }

    openMessageModal() {
        this.setState({
            messageModalDisplay: true
        });
    }

    openLoadingModal() {
        this.setState({
            loadingModalDisplay: true
        });
    }

    closeLoadingModal() {
        this.setState({
            loadingModalDisplay: false
        });
    }

    requestRsvp(status) {
        this.openLoadingModal();
        var _this = this;
        Request.post(window.location.href + '/rsvp')
            .send({token: this.props.token, status: status})
            .end((err, res) => {
                var result = JSON.parse(res.text);
                if(err) {
                    console.log(res);
                    if (result && result.error && result.error.code) {
                        _this.setAlertModalErrorCode(result.error.code)
                    }
                    _this.closeLoadingModal();
                    _this.openAlertModal();
                } else {
                    if (result.status !== 'ok') {
                        _this.setAlertModalErrorCode(result.error.code);
                        _this.closeLoadingModal();
                        _this.openAlertModal();
                    } else {
                        _this.closeLoadingModal();
                        _this.openMessageModal();
                        _this.setUserStatus(result.result.status);
                    }
                }
            });
    }

    render() {
        return (
            <section>
                <div className="rsvp">
                    {this.state.userStatus === 0 ? <button className="rsvpButton" onClick={this.openModal.bind(this)}>出欠のご回答</button> : null}
                    {this.state.userStatus === 1 ? <button className="rsvpButton" onClick={this.openModal.bind(this)}>出欠を変更する（御欠席で回答済み）</button> : null}
                    {(this.state.userStatus === 2 || this.state.userStatus === 10) ? <button className="rsvpButton" onClick={this.openModal.bind(this)}>出欠を変更する（御出席で回答済み）</button> : null}
                </div>
                {this.state.userStatus === 2 && parseInt(this.props.giftType) === 0 ?
                    (this.props.giftName === ''  ?
                        <div className="rsvp"><a href={location.href + '/present'}><button className="rsvpButton">お土産を選択する</button></a></div> :
                        <div className="rsvp"><p>お土産： {this.props.giftName}</p></div>
                    ) : null
                }
                {this.state.userStatus > 0 ?  <div className="rsvp"><p>※出欠のご確定は11月4日までにお願いいたします</p></div> : null}
                <LoadingModal display={this.state.loadingModalDisplay} />
                <RsvpModal display={this.state.rsvpModalDisplay} closeModal={this.closeModal.bind(this)} requestRsvp={this.requestRsvp.bind(this)} />
                <AlertModal display={this.state.alertModalDisplay} closeModal={this.closeAlertModal.bind(this)} errorCode={this.state.errorCode} />
                <MessageModal display={this.state.messageModalDisplay} />
            </section>
        );
    }
}



ReactDom.render(
    <Rsvp
        token={document.getElementById('token').getAttribute('data-token')}
        status={document.getElementById('userStatus').getAttribute('data-user-status')}
        giftType={document.getElementById('giftType').getAttribute('data-gift-type')}
        giftName={document.getElementById('giftName').getAttribute('data-gift-name')}
    />,
    document.getElementById('container')
);