import React from "react";
import ReactDom from "react-dom";
import Modal from 'react-modal';
import Request from 'superagent';

let modalWindowStyle = {
    content : {
        top: '50%',
        left: '50%',
        right: 'auto',
        bottom: 'auto',
        transform: 'translate(-50%, -50%)',
        padding: 0,
        backgroundColor: '#f5f4d1',
        border: 'solid 1px #8BC34A'
    }
};

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
                   style={modalWindowStyle}
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
        this.props.closeAlertModal();
    }

    onRequestClose() {
        this.props.closeAlertModal();
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   style={modalWindowStyle}
            >
                <div>
                    <p>不明なエラーが発生しました。ページをリロードし、しばらくおいてから試してください。</p>
                    <p>何度もこのメッセージが表示される場合、お手数ですが新郎新婦までご連絡ください。</p>
                    <p>エラーコード: {this.props.errorCode}</p>
                    <button className="rsvpButton" onClick={this._handleClick.bind(this)}>確認</button>
                </div>
            </Modal>
        );
    }
}

class ConfirmModal extends React.Component {
    // TODO 出欠確定後のモーダルを作成し、出席なら引き出物ページへ、欠席ならTOPへ飛ばす
}

class Rsvp extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            rsvpModalDisplay: false,
            alertModalDisplay: false,
            errorCode: 'unknown error',
            userStatus: parseInt(props.status)
        };
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

    requestRsvp(status) {
        var _this = this;
        Request.post(window.location.href + '/rsvp')
            .send({token: this.props.token, status: status})
            .end((err, res) => {
                if(err) {
                    console.log(err);
                    _this.openAlertModal();
                    window.alert('不明なエラーが発生しました。ページをリロードし再度試してください。')
                } else {
                    var result = JSON.parse(res.text);
                    if (result.status !== 'ok') {

                    } else {
                        _this.setAlertModalErrorCode(result.error.code);
                        _this.openAlertModal();
                    }

                    console.log(res);
                }
            });
    }

    render() {
        var presentUrl = location.href + '/present';
        return (
            <section>
                <div className="rsvp">
                    {this.state.userStatus === 0 ? <button className="rsvpButton" onClick={this.openModal.bind(this)}>出欠のご回答</button> : null}
                    {this.state.userStatus === 1 ? <button className="rsvpButton" onClick={this.openModal.bind(this)}>出欠を変更する（御欠席で回答済み）</button> : null}
                    {(this.state.userStatus === 2 || this.state.userStatus === 10) ? <button className="rsvpButton" onClick={this.openModal.bind(this)}>出欠を変更する（御出席で回答済み）</button> : null}
                </div>
                {this.state.userStatus === 2 ?  <div className="rsvp"><a href={presentUrl}><button className="rsvpButton">お土産を選択する</button></a></div> : null}
                {this.state.userStatus > 0 ?  <div className="rsvp"><p>※出欠は○月○日まで変更可能です</p></div> : null}
                <RsvpModal display={this.state.rsvpModalDisplay} closeModal={this.closeModal.bind(this)} requestRsvp={this.requestRsvp.bind(this)} />
                <AlertModal display={this.state.alertModalDisplay} closeModal={this.closeAlertModal.bind(this)} errorCode={this.state.errorCode} />
            </section>
        );
    }
}



ReactDom.render(
    <Rsvp token={document.getElementById('token').getAttribute('data-token')} status={document.getElementById('userStatus').getAttribute('data-user-status')} />,
    document.getElementById('container')
);